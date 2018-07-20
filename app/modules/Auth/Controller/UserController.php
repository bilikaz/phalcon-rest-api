<?php
declare(strict_types=1);

namespace App\Auth\Controller;

use GuzzleHttp\Psr7\ServerRequest;

use App\Library\Api\Controller,
    App\Library\Api\Auth\Entity\Auth,
    App\Library\Api\Request\Mapper\JsonRequestMapper,
    App\Library\Api\Request\Mapper\Entity\BasicRequestEntity;

use App\Authenticator\Exception\AuthenticatorException,
    App\Auth\Exception\CurrentAuthOnlyException;


class UserController extends Controller
{

    public function getRequestMappers()
    {
        return [
            'post' => 'App\Auth\Request\Mapper\Psr7RequestMapper',
            'put' => function() {
                return new JsonRequestMapper([
                    'code',
                ], 'App\Library\Api\Request\Mapper\Entity\BasicRequestEntity');
            },
        ];
    }

    public function getResponseMappers()
    {
        return [
            'post' => 'App\Auth\Response\Mapper\Psr7ResponseMapper',
        ];
    }

    public function post(ServerRequest $request)
    {
        return $this->authManager->handleUserRequest($request);
    }

    public function get(string $id)
    {
        if ($id != 'current') {
            throw new CurrentAuthOnlyException();
        }
        return $this->auth;
    }

    public function put(Auth $auth, BasicRequestEntity $request)
    {
        return $this->authManager->handleAuthenticateRequest($auth, $request);
    }
}
