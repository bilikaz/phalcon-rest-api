<?php
declare(strict_types=1);

namespace App\Authenticator\Exception;

use App\Library\Api\Exception\ApiException;

class AuthenticatorNotFoundException extends ApiException
{

    protected $responseCode = 400;
    protected $errorCode = 4205;
    protected $errorMessage = 'Authenticator not found';

}
