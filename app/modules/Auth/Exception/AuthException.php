<?php
declare(strict_types=1);

namespace App\Auth\Exception;

use League\OAuth2\Server\Exception\OAuthServerException;

use App\Library\Api\Exception\ApiException;

class AuthException extends ApiException
{

    protected $responseCode = 400;

    public function createFromOauthException(OAuthServerException $exception)
    {
        $this->errorCode = $exception->getCode() == 4 ? 4200 : 4201;
        $this->errorMessage = $exception->getMessage();
        if ($exception->getHint()) {
            $this->message = $exception->getHint();
        }
    }

}
