<?php
declare(strict_types=1);

namespace App\Client\Repository;

use App\Library\Repository\AbstractRepository;

class ClientTypeUserScopesRepository extends AbstractRepository
{

    const MODEL = 'App\Client\Model\ClientTypeUserScope';
    const ALIAS = 'scope';
    const ID = ['client_type_id', 'scope_id'];

}
