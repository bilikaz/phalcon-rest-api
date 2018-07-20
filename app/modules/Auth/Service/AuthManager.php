<?php
declare(strict_types=1);

namespace App\Auth\Service;

use DateInterval;

use League\OAuth2\Server\ResourceServer,
    League\OAuth2\Server\AuthorizationServer,
    League\OAuth2\Server\CryptKey,
    League\OAuth2\Server\Grant\ClientCredentialsGrant,
    League\OAuth2\Server\Grant\PasswordGrant,
    League\OAuth2\Server\Grant\RefreshTokenGrant,
    League\OAuth2\Server\Exception\OAuthServerException;

use GuzzleHttp\Psr7\ServerRequest,
    GuzzleHttp\Psr7\Response,
    GuzzleHttp\Psr7\LazyOpenStream;

use App\Library\Service\AbstractService,
    App\Library\Api\Auth\Entity\Auth,
    App\Library\Api\Route\RouteInterface,
    App\Library\Api\Auth\Manager\AuthManagerInterface,
    App\Library\Api\Config,
    App\Library\Api\Request,
    App\Library\Api\Request\Mapper\Entity\BasicRequestEntity,
    App\Auth\Repository\AuthAccessTokensRepository,
    App\Auth\Repository\AuthCodesRepository,
    App\Auth\Repository\AuthRefreshTokensRepository,
    App\Auth\Repository\AuthScopesRepository,
    App\Auth\Grant\SystemGrant,
    App\Auth\Exception\AuthException,
    App\Client\Repository\ClientsRepository,
    App\User\Repository\UsersRepository,
    App\Authenticator\Repository\UserAuthenticatorsRepository,
    App\Authenticator\Service\AuthenticatorManager;


class AuthManager extends AbstractService implements AuthManagerInterface
{

    protected function getConfig(): Config
    {
        return $this->resolveService('config');
    }

    protected function getRequest(): Request
    {
        return $this->resolveService('request');
    }

    protected function getAuthAccessTokensRepository(): AuthAccessTokensRepository
    {
        return $this->resolveService('authAccessTokensRepository');
    }

    protected function getAuthCodesRepository(): AuthCodesRepository
    {
        return $this->resolveService('authCodesRepository');
    }

    protected function getAuthRefreshTokensRepository(): AuthRefreshTokensRepository
    {
        return $this->resolveService('authRefreshTokensRepository');
    }

    protected function getAuthScopesRepository(): AuthScopesRepository
    {
        return $this->resolveService('authScopesRepository');
    }

    protected function getClientsRepository(): ClientsRepository
    {
        return $this->resolveService('clientsRepository');
    }

    protected function getUsersRepository(): UsersRepository
    {
        return $this->resolveService('usersRepository');
    }

    protected function getUserAuthenticatorsRepository(): UserAuthenticatorsRepository
    {
        return $this->resolveService('userAuthenticatorsRepository');
    }

    protected function getAuthenticatorManager(): AuthenticatorManager
    {
        return $this->resolveService('authenticatorManager');
    }

    private function getAuthorizationServer()
    {
        if (!isset($this->authorizationServer)) {
            $this->authorizationServer = new AuthorizationServer(
                $this->getClientsRepository(),
                $this->getAuthAccessTokensRepository(),
                $this->getAuthScopesRepository(),
                BASE_PATH . $this->getConfig()->modules->auth->privateKey,
                $this->getConfig()->modules->auth->password
            );
        }
        return $this->authorizationServer;
    }

    private function getAccessTokenTTL()
    {
        return new DateInterval('PT1H'); //1 hour
    }

    private function getRefreshTokenTTL()
    {
        return new DateInterval('P1M'); //1 month
    }

    public function handleClientRequest(ServerRequest $request)
    {
        $server = $this->getAuthorizationServer();
        $grant = new ClientCredentialsGrant();
        $server->enableGrantType($grant, $this->getAccessTokenTTL());

        try {
            $response = $server->respondToAccessTokenRequest($request, new Response());
        } catch (OAuthServerException $e) {
            $exception = new AuthException();
            $exception->createFromOauthException($e);
            throw $exception;
        }
        return $response;
    }

    public function handleUserRequest(ServerRequest $request)
    {
        $server = $this->getAuthorizationServer();
        $grant = new PasswordGrant(
            $this->getUsersRepository(),
            $this->getAuthRefreshTokensRepository()
        );
        $grant->setRefreshTokenTTL($this->getRefreshTokenTTL());
        $server->enableGrantType($grant, $this->getAccessTokenTTL());

        try {
            $response = $server->respondToAccessTokenRequest($request, new Response());
        } catch (OAuthServerException $e) {
            $exception = new AuthException();
            $exception->createFromOauthException($e);
            throw $exception;
        }
        return $response;
    }

    public function handleTokenRequest(ServerRequest $request)
    {
        $server = $this->getAuthorizationServer();
        $grant = new RefreshTokenGrant($this->getAuthRefreshTokensRepository());
        $grant->setRefreshTokenTTL($this->getRefreshTokenTTL());
        $server->enableGrantType($grant, $this->getAccessTokenTTL());

        try {
            $response = $server->respondToAccessTokenRequest($request, new Response());
        } catch (OAuthServerException $e) {
            $exception = new AuthException();
            $exception->createFromOauthException($e);
            throw $exception;
        }
        return $response;
    }

    public function handleAuthenticateRequest(Auth $auth, BasicRequestEntity $request)
    {
        $userAuthenticatorModel = $this->getUserAuthenticatorsRepository()->getCurrentByUserId($auth->userId);
        $this->getAuthenticatorManager()->validateUserAuthenticator($userAuthenticatorModel, $request);

        $grant = $this->getSystemGrant();
        $auth->scopes[] = 'authenticated';
        $client = $this->getClientsRepository()->getById($auth->clientId, true);
        $scopes = $this->getAuthScopesRepository()->fromArray($auth->scopes);
        $tokens = $grant->getNewTokens($this->getAccessTokenTTL(), $client, $auth->userId, $scopes);

        return $tokens;
    }

    public function handleAuth(RouteInterface $route): Auth
    {
        $auth = new Auth();

        $request = new ServerRequest(
            $this->getRequest()->getMethod(),
            $this->getRequest()->getURI(),
            $this->getRequest()->getHeaders(),
            new LazyOpenStream('php://input', 'r+'),
            '1.1',
            $_SERVER
        );

        $server = new ResourceServer(
            $this->getAuthAccessTokensRepository(),
            BASE_PATH . $this->getConfig()->modules->auth->publicKey
        );

        try {
            $attributes = $server->validateAuthenticatedRequest($request)->getAttributes();
        } catch (OAuthServerException $e) {
            $exception = new AuthException();
            $exception->createFromOauthException($e);
            throw $exception;
        }

        $auth->clientId = $attributes['oauth_client_id'];
        $auth->userId = $attributes['oauth_user_id'];
        $auth->scopes = $attributes['oauth_scopes'];
        return $auth;
    }


    public function getSystemGrant()
    {
        $grant = new SystemGrant(
            $this->getUsersRepository(),
            $this->getAuthRefreshTokensRepository()
        );
        $grant->setRefreshTokenTTL($this->getRefreshTokenTTL());
        $grant->setAccessTokenRepository($this->getAuthAccessTokensRepository());
        $grant->setClientRepository($this->getClientsRepository());
        $grant->setScopeRepository($this->getAuthScopesRepository());
        $grant->setPrivateKey(new CryptKey(BASE_PATH . $this->getConfig()->modules->auth->privateKey));
        $grant->setEncryptionKey($this->getConfig()->modules->auth->password);
        return $grant;
    }

}
