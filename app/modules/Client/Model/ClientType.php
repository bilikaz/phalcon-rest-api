<?php
declare(strict_types=1);

namespace App\Client\Model;

use App\Library\Repository\Model;

class ClientType extends Model
{

    public $client_type_id;
    public $title;

    public function getSource()
    {
        return 'client_types';
    }

}
