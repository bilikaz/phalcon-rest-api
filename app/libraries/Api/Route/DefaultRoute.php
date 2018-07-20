<?php
declare(strict_types=1);

namespace App\Library\Api\Route;

use Phalcon\Text;

use App\Library\Api\Exception\UrlNotFoundException,
    App\Library\Api\Exception\EntityNotFoundException,
    App\Library\Api\Request\Mapper\RequestMapperInterface;

class DefaultRoute implements RouteInterface
{

    protected $namespace;
    protected $mask;
    protected $method;
    protected $uri;
    protected $prefix;
    protected $authEnabled = true;
    protected $queue;

    public function __construct(bool $authEnabled = null)
    {
        if (isset($authEnabled)) {
            $this->setAuthEnabled($authEnabled);
        }
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

    public function setNamespace(string $namespace)
    {
        $this->namespace = $namespace;
        return $this;
    }

    protected function creatMask()
    {
        preg_match('/^' . $this->prefix . '/', $this->uri, $prefix);
        $prefix = $prefix[0];
        $uri = preg_replace('/^' . $this->prefix . '/', '',  $this->uri);
        $parts = explode('/', $this->uri);
        $mask = [];
        foreach ($parts as $i => $part) {
            if ($i % 2) {
                if (!in_array($part, ['list'])) {
                    $mask[] = ':id';
                } else {
                    $mask[] = $part;
                }
            } else {
                $mask[] = $part;
            }
        }
        return $prefix . implode('/', $mask);
    }

    public function getMask()
    {
        if (!isset($this->mask)) {
            $this->mask = $this->creatMask();
        }
        return $this->mask;
    }

    public function setMask(string $mask)
    {
        $this->mask = $mask;
        return $this;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function setMethod(string $method)
    {
        $this->method = $method;
        return $this;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function setUri(string $uri)
    {
        $this->uri = $uri;
        return $this;
    }

    public function getPrefix()
    {
        return $this->prefix;
    }

    public function setPrefix(string $prefix)
    {
        $this->prefix = $prefix;
        return $this;
    }

    public function getAuthEnabled()
    {
        return $this->authEnabled;
    }

    public function setAuthEnabled(bool $authEnabled)
    {
        $this->authEnabled = $authEnabled;
        return $this;
    }

    protected function createQueue()
    {
        $uri = preg_replace('/^' . $this->prefix . '/', '',  $this->uri);
        if (!strlen($uri)) {
            throw new UrlNotFoundException();
        }
        $parts = explode('/', $uri);
        $module = '\\' . Text::camelize($parts[0], '_-');
        $count = count($parts);

        if ($count == 1 && !in_array($this->method, ['get', 'post'])) {
            //only post can be done to object
            throw new UrlNotFoundException();
        } elseif ($count % 2 == 0 && $this->method == 'post') {
            //post can't be done to id
            throw new UrlNotFoundException();
        }

        $current = 0;
        $namespace = '\\' . $this->namespace;
        while ($current < $count) {
            $namespace = $namespace . '\\' . Text::camelize($parts[$current], '_-');
            if ($current + 1 == $count) {
                if ($this->method == 'post') {
                    $this->queue[] = [
                        'controller' => $namespace  . 'Controller',
                        'action' => 'post',
                    ];
                } else {
                    $this->queue[] = [
                        'controller' => $namespace  . 'Controller',
                        'action' => 'list',
                    ];
                }
            } elseif ($current + 2 <= $count) {
                $this->queue[] = [
                    'controller' => $namespace . 'Controller',
                    'action' => 'get',
                    'params' => [$parts[$current + 1]],
                ];
                if ($current + 2 == $count && $this->method != 'get') {
                    $this->queue[] = [
                        'controller' => $namespace . 'Controller',
                        'action' => $this->method,
                    ];
                }
            }
            $current = $current + 2;
        }
    }

    protected function validateQueue()
    {
        $className = null;
        foreach ($this->queue as $id => $call) {
            if ($className != $call['controller']) {
                $className = $call['controller'];
                if (!class_exists($className)) {
                    throw new UrlNotFoundException();
                }
                $controller = new $className();
            }
            if (!method_exists($controller, $call['action'])) {
                throw new UrlNotFoundException();
            }
            $this->queue[$id]['controller'] = $controller;
        }
    }

    public function handle(RequestMapperInterface $defaultRequestMapper)
    {
        $response = null;
        $chain = false;
        foreach ($this->queue as $call) {
            $controller = $call['controller'];
            $action = $call['action'];
            if (isset($response)) {
                $params = [$response];
            } else {
                if ($chain) {
                    throw new EntityNotFoundException();
                }
                $params = [];
            }
            if (isset($call['params']) && $call['params']) {
                $params = array_merge($params, $call['params']);
            }
            if ($action != 'get') {
                $params[] = $controller->mapRequest($action, $defaultRequestMapper);
            }
            $response = $controller->{$action}(...$params);
            $chain = true;
        }
        return $controller->mapResponse($action, $response);
    }

    public function resolve()
    {
        $this->createQueue();
        $this->validateQueue();
    }
}
