<?php
declare(strict_types=1);

namespace App\Library\Api\Request\Mapper;

use App\Library\Api\Exception\InvalidRequestException;

trait JsonRequestTrait
{

    protected function validateContentType($contentType)
    {
        if (strtolower($contentType) !== 'application/json') {
            throw new InvalidRequestException('Only application/json request is supported');
        }
    }

    protected function bodyToArray($requestBody)
    {
        if ($requestBody) {
            $array = json_decode($requestBody, true);
            if (!$array) {
                if (preg_replace('/[\s\n\r\t]+/', '', $requestBody) !== '{}') {
                    throw new InvalidRequestException('Malformed json request');
                } else {
                    $array = [];
                }
            }
        } else {
            $array = [];
        }
        return $array;
    }

}
