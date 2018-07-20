<?php
declare(strict_types=1);

namespace App\Library\Api\Exception;

use Exception;

class ApiException extends Exception
{

    protected $responseCode = 500;
    protected $errorCode = 5000;
    protected $errorMessage = 'Internal error';

    public function getResponseCode(): int
    {
        return $this->responseCode;
    }

    public function getResponse()
    {
        $response = [
            'error' => [
                'code' => $this->errorCode,
                'message' => $this->errorMessage,
            ],
        ];

        if ($this->getMessage()) {
            $response['error']['details'] = $this->getMessage();
        }
        return $response;
    }

}
