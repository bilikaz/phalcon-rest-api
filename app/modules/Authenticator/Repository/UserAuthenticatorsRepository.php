<?php
declare(strict_types=1);

namespace App\Authenticator\Repository;

use App\Library\Repository\AbstractRepository;

use App\User\Model\User,
    App\Authenticator\Model\UserAuthenticator,
    App\Authenticator\Exception\EnableCurrentAuthenticatorException;

class UserAuthenticatorsRepository extends AbstractRepository
{

    const MODEL = 'App\Authenticator\Model\UserAuthenticator';
    const ALIAS = 'user_authenticator';
    const ID = ['user_id', 'authenticator_id'];

    public function getListByUserId($userId)
    {
        $query = $this->query()
            ->andWhere('user_authenticator.user_id = :user_id:', ['user_id' => $userId]);

        return $this->getResults($query, false);
    }

    public function getCurrentByUserId($userId)
    {
        $query = $this->query()
            ->join($this->usersRepository::MODEL, 'user_authenticator.user_id = user.user_id AND user_authenticator.authenticator_id = user.authenticator_id', 'user')
            ->andWhere('user_authenticator.user_id = :user_id:', ['user_id' => $userId]);

        return $this->getResult($query, new EnableCurrentAuthenticatorException());
    }

}
