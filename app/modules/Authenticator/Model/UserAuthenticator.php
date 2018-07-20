<?php
declare(strict_types=1);

namespace App\Authenticator\Model;

use App\Library\Repository\Model;

class UserAuthenticator extends Model
{

    public $authenticator_id;
    public $user_id;
    public $setup;
    public $params;

    public function getSource()
    {
        return 'user_authenticators';
    }

    public function afterFetch()
    {
        if ($this->params) {
            $this->params = json_decode($this->params);
        }
    }

    public function beforeSave()
    {
        if ($this->params) {
            $this->params = json_encode($this->params);
        } else {
            $this->params = null;
        }
    }

    public function afterSave()
    {
        if ($this->params) {
            $this->params = json_decode($this->params);
        }
    }

}
