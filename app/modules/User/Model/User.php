<?php
declare(strict_types=1);

namespace App\User\Model;

use App\Auth\Model\User as UserModel;

class User extends UserModel
{

    public $user_id;
    public $user_type_id;
    public $email;
    public $password;
    public $status;
    public $authenticator_id;
    public $timestamp_created;
    public $timestamp_updated;

    public function initialize()
    {
        $this->setSource('users');
    }

    public function beforeValidation()
    {
        if (!isset($this->timestamp_created)) {
            $this->timestamp_created = time();
            $this->password = password_hash($this->password, PASSWORD_DEFAULT);
            $this->status = 'active';
            if (!$this->user_id) {
                $this->user_id = $this->usersRepository->getNextId();
            }
        }
        $this->timestamp_updated = time();
    }

    public function toArray($columns = null): array
    {
        $array = parent::toArray($columns);

        unset($array['password']);
        unset($array['timestamp_updated']);
        return $array;
    }

}
