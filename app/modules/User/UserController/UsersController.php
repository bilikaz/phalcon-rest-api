<?php
declare(strict_types=1);

namespace App\User\UserController;

use App\Library\Api\Controller,
    App\Library\Api\Request\Mapper\Entity\BasicRequestEntity,
    App\Library\Api\Exception\EntityNotFoundException;

class UsersController extends Controller
{

    public function get(string $id)
    {
        if ($id != 'current' && $id != $this->auth->userId) {
            throw new EntityNotFoundException();
        }
        return $this->usersRepository->getById($this->auth->userId, true);
    }

}
