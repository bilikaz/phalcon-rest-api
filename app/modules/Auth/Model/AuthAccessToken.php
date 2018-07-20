<?php
declare(strict_types=1);

namespace App\Auth\Model;

use DateTime;

use App\Library\Repository\Model;

use League\OAuth2\Server\Entities\AccessTokenEntityInterface,
    League\OAuth2\Server\Entities\ClientEntityInterface,
    League\OAuth2\Server\Entities\Traits\AccessTokenTrait,
    League\OAuth2\Server\Entities\ScopeEntityInterface;

use App\Auth\Repository\Scopes;

class AuthAccessToken extends Model implements AccessTokenEntityInterface
{

    use AccessTokenTrait;

    public $access_token_id;
    public $client_id;
    public $user_id;
    public $scopes = [];
    public $timestamp_expire;
    public $timestamp_created;
    public $timestamp_updated;

    private $clientModel;

    public function getSource()
    {
        return 'auth_access_tokens';
    }

    public function beforeValidation()
    {
        if (!isset($this->timestamp_created)) {
            $this->timestamp_created = time();
        }
        $this->timestamp_updated = time();
    }

    public function afterFetch()
    {
        $this->scopes = $this->authScopesRepository->fromArray(explode('|', $this->scopes));
    }

    public function beforeSave()
    {
        $this->scopes = implode('|', $this->authScopesRepository->toArray($this->scopes));
    }

    public function afterSave()
    {
        $this->scopes = $this->authScopesRepository->fromArray(explode('|', $this->scopes));
    }

    public function addScope(ScopeEntityInterface $scope)
    {
        $this->scopes[$scope->getIdentifier()] = $scope;
    }

    public function getScopes()
    {
        return array_values($this->scopes);
    }

    public function getIdentifier()
    {
        return $this->access_token_id;
    }

    public function setIdentifier($id)
    {
        $this->access_token_id = $id;
    }

    public function getUserIdentifier()
    {
        return $this->user_id;
    }

    public function setUserIdentifier($id)
    {
        $this->user_id = $id;
    }

    public function getExpiryDateTime()
    {
        return new DateTime('@' . $this->timestamp_expire);
    }

    public function setExpiryDateTime(DateTime $dateTime)
    {
        $this->timestamp_expire = $dateTime->getTimestamp();
    }

    public function getClient()
    {
        if (!$this->clientModel) {
            $this->clientModel = $this->clientsRepository->getById($this->client_id, true);
        }
        return $this->clientModel;
    }

    public function setClient(ClientEntityInterface $clientModel)
    {
        $this->clientModel = $clientModel;
        $this->client_id = $clientModel->client_id;
    }
}
