<?php
declare(strict_types=1);

namespace App\Auth\Model;

use League\OAuth2\Server\Entities\UserEntityInterface;

use App\Library\Repository\Model;

class User extends Model implements UserEntityInterface
{

    public $user_id;

    public function getIdentifier()
    {
        return $this->user_id;
    }

    public function setIdentifier($id)
    {
        $this->user_id = $id;
    }

}
