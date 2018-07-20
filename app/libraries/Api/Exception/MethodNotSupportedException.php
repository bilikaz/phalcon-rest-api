<?php
declare(strict_types=1);

namespace App\Library\Api\Exception;

class MethodNotSupportedException extends ApiException
{

    protected $responseCode = 500;
    protected $errorCode = 5001;
    protected $errorMessage = 'Method not supported';

}
