<?php
declare(strict_types=1);

namespace App\Authenticator\Exception;

use App\Library\Api\Exception\ApiException;

class DisableCurrentAuthenticatorException extends ApiException
{

    protected $responseCode = 400;
    protected $errorCode = 4207;
    protected $errorMessage = 'In order to configure this authenticator you must disable current active';

}
