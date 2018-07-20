<?php
declare(strict_types=1);

namespace App\User\Response\Mapper;

use App\Library\Api\Response\Mapper\ResponseMapperInterface;

use App\User\Repository\UsersRepository;

class UserAuthenticatorListMapper implements ResponseMapperInterface
{

    private $usersRepository;

    public function __construct(UsersRepository $usersRepository)
    {
        $this->usersRepository = $usersRepository;
    }

    public function mapResponse($response)
    {
        $userAuthenticatorMapper = new UserAuthenticatorMapper($this->usersRepository);
        $list = [];
        if ($response) {
            foreach ($response as $userAuthenticatorModel) {
                $list[] = $userAuthenticatorMapper->mapResponse($userAuthenticatorModel);
            }
        }
        return ['user_authenticators' => $list];
    }

}
