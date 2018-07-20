<?php
declare(strict_types=1);

namespace App\Auth\Repository;

use League\OAuth2\Server\Entities\ClientEntityInterface;

trait UsersRepositoryTrait
{

    public function getUserEntityByUserCredentials($username, $password, $grantType, ClientEntityInterface $clientEntity)
    {
        $query = $this->query('user')
            ->andWhere('user.status = :status:', ['status' => 'active'])
            ->andWhere('user.email = :email:', ['email' => $username])
            ->limit(1);

        $userModel = $this->getResult($query, null);
        if (!$userModel || !password_verify($password, $userModel->password)) {
            return null;
        }

        return $userModel;
    }

}
