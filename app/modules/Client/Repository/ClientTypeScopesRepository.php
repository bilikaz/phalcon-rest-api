<?php
declare(strict_types=1);

namespace App\Client\Repository;

use App\Library\Repository\AbstractRepository;

class ClientTypeScopesRepository extends AbstractRepository
{

    const MODEL = 'App\Client\Model\ClientTypeScope';
    const ALIAS = 'scope';
    const ID = ['client_type_id', 'scope_id'];

}
