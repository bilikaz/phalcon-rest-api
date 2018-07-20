<?php
declare(strict_types=1);

namespace App\Library\Api\Exception;

class InvalidRequestException extends ApiException
{

    protected $responseCode = 500;
    protected $errorCode = 5002;
    protected $errorMessage = 'Invalid request';

}
