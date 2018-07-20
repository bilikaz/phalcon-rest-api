<?php
declare(strict_types=1);

namespace App\Library\Api\Exception;


class UrlNotFoundException extends ApiException
{

    protected $responseCode = 404;
    protected $errorCode = 4040;
    protected $errorMessage = 'Url not found';

}
