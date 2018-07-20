<?php
declare(strict_types=1);

namespace App\Library\Api\Auth\Manager;

use App\Library\Api\Auth\Entity\Auth,
    App\Library\Api\Route\RouteInterface;

interface AuthManagerInterface
{

    public function handleAuth(RouteInterface $route): Auth;

}
