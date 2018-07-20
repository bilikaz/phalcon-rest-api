<?php
declare(strict_types=1);

namespace App\Authenticator\Service;

use App\Library\Service\AbstractService,
    App\Library\Api\Request\Mapper\Entity\BasicRequestEntity,
    App\Library\Api\Config;

use App\User\Model\User,
    App\Authenticator\Model\UserAuthenticator,
    App\Authenticator\Repository\UserAuthenticatorsRepository,
    App\User\Repository\UsersRepository,
    App\Authenticator\Service\Authenticator\AbstractAuthenticator,
    App\Authenticator\Exception\AuthenticatorNotFoundException,
    App\Authenticator\Exception\DisableCurrentAuthenticatorException;

class AuthenticatorManager extends AbstractService
{

    protected function getConfig(): Config
    {
        return $this->resolveService('config');
    }

    protected function getUserAuthenticatorsRepository(): UserAuthenticatorsRepository
    {
        return $this->resolveService('userAuthenticatorsRepository');
    }

    protected function getUsersRepository(): UsersRepository
    {
        return $this->resolveService('usersRepository');
    }

    public function getAuthenticator(UserAuthenticator $userAuthenticatorModel): AbstractAuthenticator
    {
        $className = 'App\\Authenticator\\Service\\Authenticator\\' . ucfirst($userAuthenticatorModel->authenticator_id) . 'Authenticator';
        if (!class_exists($className)) {
            throw new AuthenticatorNotFoundException();
        }
        $authenticator = new $className($this->getConfig(), $userAuthenticatorModel);
        return $authenticator;
    }

    public function addAuthenticatorForUser(User $userModel, UserAuthenticator $userAuthenticatorModel)
    {
        $userAuthenticatorModel->user_id = $userModel->user_id;
        $authenticator = $this->getAuthenticator($userAuthenticatorModel);
        $userAuthenticatorModel->params = $authenticator->getSetupParams();

        $this->getUserAuthenticatorsRepository()->create($userAuthenticatorModel);
    }

    public function updateUserAuthenticator(UserAuthenticator $userAuthenticatorModel, BasicRequestEntity $request)
    {
        $userModel = $this->getUsersRepository()->getById($userAuthenticatorModel->user_id, true);
        if (isset($userModel->authenticator_id) && $userModel->authenticator_id != $userAuthenticatorModel->authenticator_id) {
            throw new DisableCurrentAuthenticatorException();
        }
        $authenticator = $this->getAuthenticator($userAuthenticatorModel);
        $authenticator->validate($request->code);
        if ($request->status == 'active') {
            $userModel->authenticator_id = $userAuthenticatorModel->authenticator_id;
            $this->getUsersRepository()->save($userModel);
            if (!isset($userAuthenticatorModel->setup)) {
                $userAuthenticatorModel->setup = 'done';
                $this->getUserAuthenticatorsRepository()->save($userAuthenticatorModel);
            }
        } else {
            $userModel->authenticator_id = null;
            $this->getUsersRepository()->save($userModel);
        }
    }

    public function validateUserAuthenticator(UserAuthenticator $userAuthenticatorModel, BasicRequestEntity $request)
    {
        $authenticator = $this->getAuthenticator($userAuthenticatorModel);
        $authenticator->validate($request->code);
    }

}
