<?php
declare(strict_types=1);

namespace App\Authenticator\Controller;

use App\Library\Api\Controller,
    App\Library\Api\Request\Mapper\Entity\BasicRequestEntity;

class AuthenticatorsController extends Controller
{

    public function getResponseMappers()
    {
        return [
            'list' => 'App\Authenticator\Response\Mapper\AuthenticatorListMapper',
        ];
    }

    public function list(BasicRequestEntity $request)
    {
        return $this->authenticatorsRepository->getList();
    }

}
