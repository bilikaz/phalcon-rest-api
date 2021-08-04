<?php
declare(strict_types=1);

namespace App\User\Model;

use App\Library\Repository\Model;

class UserTypeScope extends Model
{

    public $user_type_id;
    public $scope_id;
    
    public function initialize()
    {
        $this->setSource('user_type_scopes');
    }

}
