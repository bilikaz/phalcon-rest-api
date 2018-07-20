<?php
declare(strict_types=1);

namespace App\User\Response\Mapper;

use App\Library\Api\Response\Mapper\ResponseMapperInterface;

use App\User\Repository\UsersRepository;

class UserAuthenticatorMapper implements ResponseMapperInterface
{

    private $usersRepository;
    private $userModels = [];

    public function __construct(UsersRepository $usersRepository)
    {
        $this->usersRepository = $usersRepository;
    }

    private function getUser($userId)
    {
        if (!isset($this->userModels[$userId])) {
            $this->userModels[$userId] = $this->usersRepository->getById($userId, true);
        }
        return $this->userModels[$userId];
    }

    public function mapResponse($response)
    {
        $response = $response->toArray();

        if (isset($response['setup'])) {
            unset($response['params']);
        }
        if ($this->getUser($response['user_id'])->authenticator_id == $response['authenticator_id']) {
            $response['status'] = 'active';
        } else {
            $response['status'] = 'disabled';
        }
        return $response;
    }

}
