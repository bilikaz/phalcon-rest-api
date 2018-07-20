<?php
declare(strict_types=1);

namespace App\Authenticator\Repository;

use App\Library\Repository\AbstractRepository;

class AuthenticatorsRepository extends AbstractRepository
{

    const MODEL = 'App\Authenticator\Model\Authenticator';
    const ALIAS = 'authenticator';
    const ID = 'authenticator_id';

    public function getList()
    {
        $query = $this->query()
            ->orderBy('authenticator.title');

        return $this->getResults($query, false);
    }

}
