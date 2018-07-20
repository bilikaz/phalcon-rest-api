<?php
declare(strict_types=1);

namespace App\Auth\Repository;

use League\OAuth2\Server\Entities\ClientEntityInterface,
    League\OAuth2\Server\Repositories\ScopeRepositoryInterface;

use App\Library\Repository\AbstractRepository;

class AuthScopesRepository extends AbstractRepository implements ScopeRepositoryInterface
{

    const MODEL = 'App\Auth\Model\AuthScope';
    const ALIAS = 'scope';
    const ID = 'scope_id';

    public function getAllScopes()
    {
        return $this->getFromCache('scopes_all', function() {
            $query = $this->query()
                ->orderBy('scope.scope_id ASC');
            return $this->getResultsByField($query, 'scope_id', null);
        }, ['scopes']);
    }

    public function getAllClientScopes($clientTypeId)
    {
        return $this->getFromCache('scopes_client_' . $clientTypeId, function() use ($clientTypeId) {
            $query = $this->query()
                ->join($this->clientTypeScopesRepository::MODEL, 'type.scope_id = scope.scope_id', 'type')
                ->andWhere('type.client_type_id = :client_type_id:', ['client_type_id' => $clientTypeId])
                ->orderBy('scope.scope_id ASC');
                return $this->getResultsByField($query, 'scope_id', null);
        }, ['scopes']);
    }

    public function getAllClientUserScopes($clientTypeId)
    {
        return $this->getFromCache('scopes_client_user_' . $clientTypeId, function() use ($clientTypeId) {
            $query = $this->query()
                ->join($this->clientTypeUserScopesRepository::MODEL, 'type.scope_id = scope.scope_id', 'type')
                ->andWhere('type.client_type_id = :client_type_id:', ['client_type_id' => $clientTypeId])
                ->orderBy('scope.scope_id ASC');
            return $this->getResultsByField($query, 'scope_id', null);
        }, ['scopes']);
    }

    public function getAllUserScopes($userTypeId)
    {
        return $this->getFromCache('scopes_user_' . $userTypeId, function() use ($userTypeId) {
            $query = $this->query()
                ->join($this->userTypeScopesRepository::MODEL, 'type.scope_id = scope.scope_id', 'type')
                ->andWhere('type.user_type_id = :user_type_id:', ['user_type_id' => $userTypeId])
                ->orderBy('scope.scope_id ASC');
            return $this->getResultsByField($query, 'scope_id', null);
        }, ['scopes']);
    }

    public function toArray($scopeModels = null)
    {
        if (!$scopeModels) {
            return [];
        }
        $array = [];
        foreach ($scopeModels as $scopeModel) {
            $array[] = $scopeModel->getIdentifier();
        }
        return $array;
    }

    public function fromArray($array = null)
    {
        if (!$array) {
            return [];
        }
        $scopeModels = $this->getAllScopes();
        foreach ($scopeModels as $key => $scopeModel) {
            if (!in_array($key, $array)) {
                unset($scopeModels[$key]);
            }
        }
        return $scopeModels;
    }

    public function getScopeEntityByIdentifier($scope_id)
    {
        $scopeModels = $this->getAllScopes();
        if (!isset($scopeModels[$scope_id])) {
            return null;
        }

        return $scopeModels[$scope_id];
    }

    public function finalizeScopes(array $scopeModels, $grantType, ClientEntityInterface $clientModel, $userIdentifier = null)
    {
        //if no scopes provided we try to load all scopes
        if (!$scopeModels) {
            $scopeModels = $this->getAllScopes();
        }
        $clientScopes = $this->getAllClientScopes($clientModel->client_type_id);
        $finalScopes = [];

        foreach ($scopeModels as $scopeModel) {
            if (isset($clientScopes[$scopeModel->scope_id])) {
                $finalScopes[$scopeModel->scope_id] = $scopeModel;
            }
        }
        if ($grantType != 'client_credentials') {
            $clientUserScopes = $this->getAllClientUserScopes($clientModel->client_type_id);
            $userModel = $this->usersRepository->getById($userIdentifier, true);
            $userScopes = $this->getAllUserScopes($userModel->user_type_id);

            if ($grantType == 'password') {
                if (isset($userScopes['authenticated'])) {
                    unset($userScopes['authenticated']);
                }
            }
            foreach ($scopeModels as $scopeModel) {
                if (isset($clientUserScopes[$scopeModel->scope_id]) && isset($userScopes[$scopeModel->scope_id])) {
                    $finalScopes[$scopeModel->scope_id] = $scopeModel;
                }
            }
        }

        return array_values($finalScopes);
    }

}
