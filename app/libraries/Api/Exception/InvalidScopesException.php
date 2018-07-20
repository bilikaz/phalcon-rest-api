<?php
declare(strict_types=1);

namespace App\Library\Api\Exception;

class InvalidScopesException extends ApiException
{

    protected $responseCode = 400;
    protected $errorCode = 4202;
    protected $errorMessage = 'The resource owner or authorization server denied the request.';

}
