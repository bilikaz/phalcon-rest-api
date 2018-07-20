<?php
declare(strict_types=1);

namespace App\Auth\Model;

use DateTime;

use League\OAuth2\Server\Entities\RefreshTokenEntityInterface,
    League\OAuth2\Server\Entities\AccessTokenEntityInterface;

use App\Library\Repository\Model;

class AuthRefreshToken extends Model implements RefreshTokenEntityInterface
{

    public $refresh_token_id;
    public $access_token_id;
    public $timestamp_expire;
    public $timestamp_created;
    public $timestamp_updated;

    private $accessTokenModel;

    public function getSource()
    {
        return 'auth_refresh_tokens';
    }

    public function beforeValidation()
    {
        if (!isset($this->timestamp_created)) {
            $this->timestamp_created = time();
        }
        $this->timestamp_updated = time();
    }

    public function getExpiryDateTime()
    {
        return new DateTime('@' . $this->timestamp_expire);
    }

    public function setExpiryDateTime(DateTime $dateTime)
    {
        $this->timestamp_expire = $dateTime->getTimestamp();
    }

    public function getIdentifier()
    {
        return $this->refresh_token_id;
    }

    public function setIdentifier($id)
    {
        $this->refresh_token_id = $id;
    }

    public function getAccessToken()
    {
        if (!$this->accessTokenModel) {
            $this->accessTokenModel = $this->authAccessTokensRepository->getById($this->access_token_id, true);
        }
        return $this->accessTokenModel;
    }

    public function setAccessToken(AccessTokenEntityInterface $accessTokenModel)
    {
        $this->accessTokenModel = $accessTokenModel;
        $this->access_token_id = $accessTokenModel->access_token_id;
    }

}
