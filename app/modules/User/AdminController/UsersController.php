<?php
declare(strict_types=1);

namespace App\User\AdminController;

use App\Library\Api\Controller,
    App\Library\Api\Request\Mapper\Entity\BasicRequestEntity,
    App\Library\Api\Exception\EntityNotFoundException;

class UsersController extends Controller
{

    public function get(string $id)
    {
        return $this->usersRepository->getById($this->auth->userId, true);
    }

    public function list(BasicRequestEntity $request)
    {
        return $this->usersRepository->getList();
    }

}
