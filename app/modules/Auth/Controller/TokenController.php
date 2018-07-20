<?php
declare(strict_types=1);

namespace App\Auth\Controller;

use GuzzleHttp\Psr7\ServerRequest;

use App\Library\Api\Controller;

class TokenController extends Controller
{

    public function getRequestMappers()
    {
        return [
            'post' => 'App\Auth\Request\Mapper\Psr7RequestMapper',
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
        return $this->authManager->handleTokenRequest($request);
    }

}
