<?php
declare(strict_types=1);

namespace App\Client\Model;

use App\Auth\Model\Client as ClientModel;

class Client extends ClientModel
{

    public $client_id;
    public $client_type_id;
    public $title;
    public $secret;
    public $redirect_url;
    public $status;
    public $timestamp_created;
    public $timestamp_updated;

    public function getSource()
    {
        return 'clients';
    }

    public function beforeValidation()
    {
        if (!isset($this->timestamp_created)) {
            $this->timestamp_created = time();
        }
        $this->timestamp_updated = time();
    }

    public function toArray($columns = null)
    {
        $array = parent::toArray($columns);

        unset($array['secret']);
        unset($array['timestamp_updated']);
        return $array;
    }

}
