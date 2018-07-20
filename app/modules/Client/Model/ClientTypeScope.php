<?php
declare(strict_types=1);

namespace App\Client\Model;

use App\Library\Repository\Model;

class ClientTypeScope extends Model
{

    public $client_type_id;
    public $scope_id;

    public function getSource()
    {
        return 'client_type_scopes';
    }

}
