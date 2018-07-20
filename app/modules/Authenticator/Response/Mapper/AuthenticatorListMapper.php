<?php
declare(strict_types=1);

namespace App\Authenticator\Response\Mapper;

use App\Library\Api\Response\Mapper\ResponseMapperInterface;

class AuthenticatorListMapper implements ResponseMapperInterface
{

    public function mapResponse($response)
    {
        $authenticatorMapper = new AuthenticatorMapper();
        $list = [];
        if ($response) {
            foreach ($response as $authenticatorModel) {
                $list[] = $authenticatorMapper->mapResponse($authenticatorModel);
            }
        }
        return ['authenticators' => $list];
    }

}
