<?php
declare(strict_types=1);

namespace App\Auth\Repository;

use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;

use App\Library\Repository\AbstractRepository;

class AuthCodesRepository extends AbstractRepository implements AuthCodeRepositoryInterface
{

    const MODEL = 'App\Auth\Model\AuthCode';
    const ALIAS = 'code';
    const ID = 'code_id';

    public function persistNewAuthCode($model)
    {
        $this->save($model);
    }

    public function revokeAuthCode($id)
    {
        // Some logic here to revoke the access token
    }

    public function isAuthCodeRevoked($id)
    {
        return false; // Access token hasn't been revoked
    }

    public function getNewAuthCode()
    {
        $model = $this->newModel();
        return $model;
    }
}
