<?php
declare(strict_types=1);

namespace App\User\Repository;

use App\Library\Repository\AbstractRepository;

class UserTypesRepository extends AbstractRepository
{

    const MODEL = 'App\User\Model\UserType';
    const ALIAS = 'type';
    const ID = 'user_type_id';

}
