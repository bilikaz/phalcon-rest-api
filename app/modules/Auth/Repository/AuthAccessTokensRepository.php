<?php
declare(strict_types=1);

namespace App\Auth\Repository;

use League\OAuth2\Server\Entities\AccessTokenEntityInterface,
    League\OAuth2\Server\Entities\ClientEntityInterface,
    League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;

use App\Library\Repository\AbstractRepository;

class AuthAccessTokensRepository extends AbstractRepository implements AccessTokenRepositoryInterface
{

    const MODEL = 'App\Auth\Model\AuthAccessToken';
    const ALIAS = 'token';
    const ID = 'access_token_id';

    public function persistNewAccessToken(AccessTokenEntityInterface $model)
    {
        $old = $this->getByFields([
            'client_id' => $model->client_id,
            'user_id' => $model->user_id,
        ], null);
        if ($old) {
            $this->delete($old);
        }
        $this->save($model);
    }

    public function revokeAccessToken($id)
    {
        // Some logic here to revoke the access token
    }

    public function isAccessTokenRevoked($id)
    {
        return false; // Access token hasn't been revoked
    }

    public function getNewToken(ClientEntityInterface $clientModel, array $scopes, $userIdentifier = null)
    {
        $model = $this->newModel();
        $model->setClient($clientModel);
        foreach ($scopes as $scope) {
            $model->addScope($scope);
        }
        $model->setUserIdentifier($userIdentifier);
        return $model;
    }
}
