<?php
declare(strict_types=1);

namespace App\Auth\Model;

use League\OAuth2\Server\Entities\AuthCodeEntityInterface;

use App\Library\Repository\Model;

use App\Auth\Repository\Scopes;

class AuthCode extends Model implements AuthCodeEntityInterface
{

    public $code_id;
    public $client_id;
    public $user_id;
    public $scopes = [];
    public $redirect_url;
    public $timestamp_expire;
    public $timestamp_created;
    public $timestamp_updated;

    public function initialize()
    {
        $this->setSource('auth_codes');
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

    public function addScope($scope)
    {
        $this->scopes[$scope->getIdentifier()] = $scope;
    }

    public function getScopes()
    {
        return array_values($this->scopes);
    }

    public function getRedirectUri()
    {
        return $this->redirect_url;
    }

    public function setRedirectUri($url)
    {
        $this->redirect_url = $url;
    }

    public function getIdentifier()
    {
        return $this->code_id;
    }

    public function setIdentifier($id)
    {
        $this->code_id = $id;
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
        return new DateTimeImmutable('@' . $this->timestamp_expire);
    }

    public function setExpiryDateTime($dateTime)
    {
        $this->timestamp_expire = $dateTime->getTimestamp();
    }

}
