<?php
declare(strict_types=1);

namespace App\Library\Api\EventsManager;

use Exception;

use Phalcon\Mvc\Micro as Application,
    Phalcon\Events\Event;

use App\Library\Api\Auth\Manager\AuthManagerInterface,
    App\Library\Api\Exception\MethodNotSupportedException,
    App\Library\Api\Exception\UrlNotFoundException,
    App\Library\Api\Request\Mapper\RequestMapperInterface,
    App\Library\Api\Router\DefaultRouter;

class RouteManager
{

    protected $authManager;
    protected $defaultRequestMapper;

    public function __construct(RequestMapperInterface $defaultRequestMapper, AuthManagerInterface $authManager = null)
    {
        $this->defaultRequestMapper = $defaultRequestMapper;
        $this->authManager = $authManager;
    }

    public function beforeHandleRoute(Event $event, Application $application)
    {
        if ($application->hasService('db')) {
            $db = $application->getSharedService('db');
            $db->begin();
        }

        $request = $application->getSharedService('request');
        $uri = preg_replace('/\?.*/', '', $request->getUri());
        $method = strtolower($request->getMethod());
        if (!in_array($method, ['get', 'post', 'put', 'delete'])) {
            throw new MethodNotSupportedException($method);
        }

        $config = $application->getSharedService('config');
        foreach ($config->modules as $module) {
            if (isset($module->routers)) {
                foreach ($module->routers as $router) {
                    if (preg_match('/^' . $router->pattern . '$/', $uri)) {
                        if (isset($router->params)) {
                            $serviceManager = $application->getSharedService('serviceManager');
                            $params = $serviceManager->resolveParams($router->params);
                        } else {
                            $params = null;
                        }
                        if (isset($router->className)) {
                            $className = $router->className;
                            $router = new $className($router->namespace, $method, $uri, $router->prefix, $params);
                        } else {
                            $router = new DefaultRouter($router->namespace, $method, $uri, $router->prefix, $params);
                        }
                        $router->resolveRoute();
                        if ($this->authManager) {
                            $auth = $router->handleAuth($this->authManager);
                            $application->setService('auth', $auth, true);
                        }
                        $defaultRequestMapper = $this->defaultRequestMapper;
                        $application->{$method}($uri, function () use ($router, $defaultRequestMapper) {
                            return $router->handleRoute($defaultRequestMapper);
                        });
                        return true;
                    }
                }
            }
        }

        throw new UrlNotFoundException();
    }

}
