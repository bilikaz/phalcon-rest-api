<?php
declare(strict_types=1);

namespace App\Library\Api\Exception;

class EntityNotFoundException extends ApiException
{

    protected $responseCode = 404;
    protected $errorCode = 4041;
    protected $errorMessage = 'Entity not found';

}
