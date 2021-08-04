<?php
declare(strict_types=1);

namespace App\Authenticator\Model;

use App\Library\Repository\Model;

class Authenticator extends Model
{

    public $authenticator_id;
    public $title;

    public function initialize()
    {
        $this->setSource('authenticators');
    }

}
