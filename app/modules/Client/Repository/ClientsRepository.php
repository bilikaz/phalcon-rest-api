<?php
declare(strict_types=1);

namespace App\Client\Repository;

use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

use App\Library\Repository\AbstractRepository,
    App\Auth\Repository\ClientsRepositoryTrait;

class ClientsRepository extends AbstractRepository implements ClientRepositoryInterface
{

    use ClientsRepositoryTrait;

    const MODEL = 'App\Client\Model\Client';
    const ALIAS = 'client';
    const ID = 'client_id';

}
