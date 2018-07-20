<?php
declare(strict_types=1);

namespace App\Client\Repository;

use App\Library\Repository\AbstractRepository;

class ClientTypesRepository extends AbstractRepository
{

    const MODEL = 'App\Client\Model\ClientType';
    const ALIAS = 'type';
    const ID = 'client_type_id';

}
