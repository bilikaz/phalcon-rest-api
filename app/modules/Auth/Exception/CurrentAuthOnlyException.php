<?php
declare(strict_types=1);

namespace App\Auth\Exception;

use App\Library\Api\Exception\ApiException;

class CurrentAuthOnlyException extends ApiException
{

    protected $responseCode = 400;
    protected $errorCode = 4203;
    protected $errorMessage = 'You can access only current auth';

}
