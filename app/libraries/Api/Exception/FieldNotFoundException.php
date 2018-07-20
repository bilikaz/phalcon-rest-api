<?php
declare(strict_types=1);

namespace App\Library\Api\Exception;


class FieldNotFoundException extends ApiException
{

    protected $responseCode = 404;
    protected $errorCode = 4042;
    protected $errorMessage = 'Field not found';

}
