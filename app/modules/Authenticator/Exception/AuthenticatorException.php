<?php
declare(strict_types=1);

namespace App\Authenticator\Exception;

use App\Library\Api\Exception\ApiException;

class AuthenticatorException extends ApiException
{

    protected $responseCode = 400;
    protected $errorCode = 4204;
    protected $errorMessage = 'Invalid authenticator code';

}
