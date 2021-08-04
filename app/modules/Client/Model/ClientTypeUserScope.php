<?php
declare(strict_types=1);

namespace App\Client\Model;

use App\Library\Repository\Model;

class ClientTypeUserScope extends Model
{

    public $client_type_id;
    public $scope_id;

    public function initialize()
    {
        $this->setSource('client_type_user_scopes');
    }

}
