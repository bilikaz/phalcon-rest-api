<?php
declare(strict_types=1);

namespace App\Authenticator\Response\Mapper;

use App\Library\Api\Response\Mapper\ResponseMapperInterface;

class AuthenticatorMapper implements ResponseMapperInterface
{

    public function mapResponse($response)
    {
        return $response->toArray();
    }

}
