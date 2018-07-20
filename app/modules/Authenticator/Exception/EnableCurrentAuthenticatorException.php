<?php
declare(strict_types=1);

namespace App\Authenticator\Exception;

use App\Library\Api\Exception\ApiException;

class EnableCurrentAuthenticatorException extends ApiException
{

    protected $responseCode = 400;
    protected $errorCode = 4207;
    protected $errorMessage = 'There is no active authenticator';

}
