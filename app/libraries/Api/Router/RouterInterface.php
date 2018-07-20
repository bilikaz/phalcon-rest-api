<?php
declare(strict_types=1);

namespace App\Library\Api\Router;

use App\Library\Api\Auth\Entity\Auth,
    App\Library\Api\Auth\Manager\AuthManagerInterface,
    App\Library\Api\Request\Mapper\RequestMapperInterface;

interface RouterInterface
{

    public function __construct(string $namespace, string $method, string $uri, string $pattern, array $params = null);

    public function resolveRoute();

    public function handleAuth(AuthManagerInterface $authManager): Auth;

    public function handleRoute(RequestMapperInterface $defaultRequestMapper);

}
