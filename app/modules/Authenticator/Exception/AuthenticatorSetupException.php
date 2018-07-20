<?php
declare(strict_types=1);

namespace App\Authenticator\Exception;

use App\Library\Api\Exception\ApiException;

class AuthenticatorSetupException extends ApiException
{

    protected $responseCode = 400;
    protected $errorCode = 4206;
    protected $errorMessage = 'Authenticator setup is already done';

}
