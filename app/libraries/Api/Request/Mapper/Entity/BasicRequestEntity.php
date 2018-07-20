<?php
declare(strict_types=1);

namespace App\Library\Api\Request\Mapper\Entity;

use stdClass;

class BasicRequestEntity extends stdClass
{

    public function toArray()
    {
        return json_decode(json_encode($this), true);
    }

}
