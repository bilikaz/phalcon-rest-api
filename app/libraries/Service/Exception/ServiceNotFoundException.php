<?php
declare(strict_types=1);

namespace App\Library\Service\Exception;

use App\Library\Api\Exception\ApiException;

class ServiceNotFoundException extends ApiException
{

    protected $responseCode = 500;
    protected $errorCode = 5100;
    protected $errorMessage = 'Service not found';

}
