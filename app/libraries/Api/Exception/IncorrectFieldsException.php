<?php
declare(strict_types=1);

namespace App\Library\Api\Exception;


class IncorrectFieldsException extends ApiException
{

    protected $responseCode = 404;
    protected $errorCode = 4043;
    protected $errorMessage = 'Incorrect fields';
    protected $fields = [];

    public function setFields(array $fields)
    {
        $this->fields = $fields;
    }

    public function getResponse()
    {
        $response = [
            'error' => [
                'code' => $this->errorCode,
                'message' => $this->errorMessage,
                'details' => $this->fields,
            ],
        ];
        return $response;
    }
}
