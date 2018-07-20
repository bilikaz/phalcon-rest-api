<?php
declare(strict_types=1);

namespace App\User\UserController\Users;

use App\Library\Api\Controller,
    App\Library\Api\Request\Mapper\Entity\BasicRequestEntity,
    App\Library\Api\Exception\EntityNotFoundException,
    App\Library\Api\Request\Mapper\JsonRequestMapper,
    App\Library\Api\Filter\InArrayFilter;

use App\User\Model\User,
    App\Authenticator\Model\UserAuthenticator,
    App\User\Response\Mapper\UserAuthenticatorMapper,
    App\User\Response\Mapper\UserAuthenticatorListMapper;

class AuthenticatorsController extends Controller
{

    public function getRequestMappers()
    {
        return [
            'post' => function() {
                return new JsonRequestMapper([
                    'authenticator_id',
                ], 'App\Authenticator\Model\UserAuthenticator');
            },
            'put' => function() {
                $statusFilter = new InArrayFilter(['active', 'disabled']);
                return new JsonRequestMapper([
                    'code',
                    'status' => [
                        'filter' => $statusFilter,
                    ]
                ], 'App\Library\Api\Request\Mapper\Entity\BasicRequestEntity');
            },
        ];
    }

    public function getResponseMappers()
    {
        return [
            'get' => function() {
                return new UserAuthenticatorMapper($this->usersRepository);
            },
            'put' => function() {
                return new UserAuthenticatorMapper($this->usersRepository);
            },
            'post' => function() {
                return new UserAuthenticatorMapper($this->usersRepository);
            },
            'list' => function() {
                return new UserAuthenticatorListMapper($this->usersRepository);
            },
        ];
    }

    public function get(User $userModel, string $id)
    {
        if ($id == 'current') {
            $id = $userModel->authenticator_id;
        }
        return $this->userAuthenticatorsRepository->getById([
            'user_id' => $userModel->user_id,
            'authenticator_id' => $id,
        ], true);
    }

    public function list(User $userModel, BasicRequestEntity $request)
    {
        return $this->userAuthenticatorsRepository->getListByUserId($userModel->user_id);
    }

    public function post(User $userModel, UserAuthenticator $userAuthenticatorModel)
    {
        $this->authenticatorManager->addAuthenticatorForUser($userModel, $userAuthenticatorModel);
        return $userAuthenticatorModel;
    }

    public function put(UserAuthenticator $userAuthenticatorModel, BasicRequestEntity $request)
    {
        $this->authenticatorManager->updateUserAuthenticator($userAuthenticatorModel, $request);
        return $userAuthenticatorModel;
    }


}
