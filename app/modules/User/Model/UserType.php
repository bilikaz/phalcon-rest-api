<?php
declare(strict_types=1);

namespace App\User\Model;

use App\Library\Repository\Model;

class UserType extends Model
{

    public $user_type_id;
    public $title;

    public function getSource()
    {
        return 'user_types';
    }

}
