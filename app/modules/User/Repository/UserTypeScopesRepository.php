<?php
declare(strict_types=1);

namespace App\User\Repository;

use App\Library\Repository\AbstractRepository;

class UserTypeScopesRepository extends AbstractRepository
{

    const MODEL = 'App\User\Model\UserTypeScope';
    const ALIAS = 'scope';
    const ID = ['user_type_id', 'scope_id'];

}
