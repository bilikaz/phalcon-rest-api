<?php
declare(strict_types=1);

namespace App\Auth\Repository;

use League\OAuth2\Server\Entities\RefreshTokenEntityInterface,
    League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;

use App\Library\Repository\AbstractRepository;

class AuthRefreshTokensRepository extends AbstractRepository implements RefreshTokenRepositoryInterface
{

    const MODEL = 'App\Auth\Model\AuthRefreshToken';
    const ALIAS = 'token';
    const ID = 'refresh_token_id';

    public function persistNewRefreshToken(RefreshTokenEntityInterface $model)
    {
        $this->save($model);
    }

    public function revokeRefreshToken($id)
    {
        // Some logic here to revoke the access token
    }

    public function isRefreshTokenRevoked($id)
    {
        return false; // Access token hasn't been revoked
    }

    public function getNewRefreshToken()
    {
        $model = $this->newModel();
        return $model;
    }
}
