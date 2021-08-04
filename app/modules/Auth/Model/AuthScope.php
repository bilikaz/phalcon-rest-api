<?php
declare(strict_types=1);

namespace App\Auth\Model;

use League\OAuth2\Server\Entities\ScopeEntityInterface;

use App\Library\Repository\Model;

class AuthScope extends Model implements ScopeEntityInterface
{

    public $scope_id;

    public function initialize()
    {
        $this->setSource('auth_scopes');
    }

    public function getIdentifier()
    {
        return $this->scope_id;
    }

    public function setIdentifier($id)
    {
        $this->scope_id = $id;
    }

    public function jsonSerialize(): array
    {
        return [$this->getIdentifier()];
    }

}
