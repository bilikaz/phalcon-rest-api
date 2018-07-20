<?php
declare(strict_types=1);

namespace App\Library\Api\Router;

use App\Library\Api\Auth\Entity\Auth,
    App\Library\Api\Auth\Manager\AuthManagerInterface,
    App\Library\Api\Request\Mapper\RequestMapperInterface,
    App\Library\Api\Exception\InvalidScopesException;

class DefaultRouter implements RouterInterface
{

    protected $prefix;
    protected $namespace;
    protected $method;
    protected $uri;
    protected $route;
    protected $defaultRoute = 'App\Library\Api\Route\DefaultRoute';
    protected $scopes = [
        ['client'],
        ['service'],
        ['admin'],
    ];

    public function __construct(string $namespace, string $method, string $uri, string $prefix, array $params = null)
    {
        $this->namespace = $namespace;
        $this->method = $method;
        $this->uri = $uri;
        $this->prefix = $prefix;
        if (isset($params)) {
            $fields = ['scopes', 'defaultRoute'];
            foreach ($fields as $field) {
                if (isset($params[$field])) {
                    $this->{$field} = $params[$field];
                }
            }
        }
    }

    public function handleAuth(AuthManagerInterface $authManager): Auth
    {
        if (!$this->route->getAuthEnabled()) {
            return new Auth();
        }
        $auth = $authManager->handleAuth($this->route);
        $this->validateScopes($auth);
        return $auth;
    }

    protected function validateScopes(Auth $auth)
    {
        $valid = true;
        if (isset($this->scopes)) {
            foreach ($this->scopes as $scopes) {
                $valid = true;
                foreach ($scopes as $scope) {
                    if (!in_array($scope, $auth->scopes)) {
                        $valid = false;
                        break;
                    }
                }
                if ($valid) {
                    break;
                }
            }
        }

        if (!$valid) {
            throw new InvalidScopesException('You don\'t have required scopes');
        }
    }

    protected function getRoute()
    {
        $route = null;
        if (class_exists($this->namespace . 'Routes')) {
            $className = $this->namespace . 'Routes';
            $routes = new $className();
            foreach ($routes->getForMethod($this->method) as $mask => $customRoute) {
                $regexp = str_replace(['-', '/', ':id'], ['\-', '\/', '([^\/]+)'], $mask);
                if (preg_match('/^' . $regexp . '$/', $this->uri)) {
                    $route = $customRoute()
                        ->setNameSpace($this->namespace)
                        ->setMask($mask)
                        ->setMethod($this->method)
                        ->setUri($this->uri)
                        ->setPrefix($this->prefix);
                }
            }
        }
        if (!$route) {
            if (is_string($this->defaultRoute)) {
                $className = $this->defaultRoute;
                $route = new $className();
            } else {
                $route = $this->defaultRoute;
            }
            $route->setNameSpace($this->namespace)
                ->setMethod($this->method)
                ->setUri($this->uri)
                ->setPrefix($this->prefix);
        }

        return $route;
    }

    public function resolveRoute()
    {
        $this->route = $this->getRoute();
        $this->route->resolve();
    }

    public function handleRoute(RequestMapperInterface $defaultRequestMapper)
    {
        return $this->route->handle($defaultRequestMapper);
    }


}
