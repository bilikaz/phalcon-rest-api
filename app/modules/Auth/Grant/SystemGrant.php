<?php
declare(strict_types=1);

namespace App\Auth\Grant;

use DateInterval;

use Psr\Http\Message\ServerRequestInterface;

use League\OAuth2\Server\Grant\AbstractGrant,
    League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface,
    League\OAuth2\Server\Repositories\UserRepositoryInterface,
    League\OAuth2\Server\ResponseTypes\ResponseTypeInterface,
    League\OAuth2\Server\CryptTrait;

class SystemGrant extends AbstractGrant
{

    use CryptTrait;

    public function __construct(
        UserRepositoryInterface $userRepository,
        RefreshTokenRepositoryInterface $refreshTokenRepository
    ) {
        $this->setUserRepository($userRepository);
        $this->setRefreshTokenRepository($refreshTokenRepository);
        $this->refreshTokenTTL = new DateInterval('P1M');
    }

    public function getNewTokens($accessTokenTTL, $clientModel, $userId, $scopes) {
        $accessToken = $this->issueAccessToken($accessTokenTTL, $clientModel, $userId, $scopes);
        $refreshToken = $this->issueRefreshToken($accessToken);

        $expireDateTime = $accessToken->getExpiryDateTime()->getTimestamp();
        return [
            'token_type'   => 'Bearer',
            'expires_in'   => $expireDateTime - (new \DateTime())->getTimestamp(),
            'access_token' => (string) $accessToken,
            'refresh_token' => $this->encrypt(json_encode([
                'client_id'        => $accessToken->getClient()->getIdentifier(),
                'refresh_token_id' => $refreshToken->getIdentifier(),
                'access_token_id'  => $accessToken->getIdentifier(),
                'scopes'           => $accessToken->getScopes(),
                'user_id'          => $accessToken->getUserIdentifier(),
                'expire_time'      => $refreshToken->getExpiryDateTime()->getTimestamp(),
            ])),
        ];
    }

    public function respondToAccessTokenRequest(
        ServerRequestInterface $request,
        ResponseTypeInterface $responseType,
        DateInterval $accessTokenTTL
    ) {
        return $responseType;
    }

    public function getIdentifier()
    {
        return 'system';
    }

}
