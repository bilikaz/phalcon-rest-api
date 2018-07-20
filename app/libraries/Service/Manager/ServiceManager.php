<?php
declare(strict_types=1);

namespace App\Library\Service\Manager;

use Phalcon\DI;

use App\Library\Service\ServiceInterface;

class ServiceManager
{

    private $di = null;

    public function __construct(DI $di)
    {
        $this->di = $di;
    }

    public function register($alias, $service)
    {
        $function = function () use ($service) {
            $serviceManager = $this->getShared('serviceManager');
            $tmp = $serviceManager->resolve($service);
            if ($tmp instanceof ServiceInterface) {
                if (isset($service->services)) {
                    $services = [];
                    foreach ($service->services as $key => $details) {
                        $services[$key] = $details;
                    }
                    $tmp->registerServices($services);
                }
                $tmp->setServiceManager($serviceManager);
            }
            return $tmp;
        };

        if (is_object($service) && isset($service->share) && !$service->shared) {
            $this->di->set($alias, $function);
        } else {
            //only shared services can be resolved on load
            if (is_object($service) && isset($service->resolve) && $service->resolve) {
                $function = $function();
            }
            $this->di->setShared($alias, $function);
        }
    }

    public function resolveParams($params, $resulvedParams = [])
    {
        foreach ($params as $key => $value) {
            if (is_object($value) && isset($value->collection)) {
                $resulvedParams[$key] = $this->resolveParams($value->collection);
            } elseif (is_object($value) && (isset($value->className) || isset($value->di))) {
                //service depends on other service or class
                $resulvedParams[$key] = $this->resolve($value);
            } elseif (is_object($value)) {
                $resulvedParams[$key] = $value->toArray();
            } else {
                $resulvedParams[$key] = $value;
            }
        }
        return $resulvedParams;
    }

    public function resolve($service)
    {
		if (is_string($service)) {
			return new $service();
        }
		if (isset($service->di)) {
            if (isset($service->shared) && !$service->shared) {
                return $this->di->get($service->di);
            }
            return $this->di->getShared($service->di);
        }

        $params = [];
		if (isset($service->signature)) {
			foreach ($service->signature as $key) {
				$params[$key] = null;
			}
		}
		if (isset($service->params)) {
            $params = $this->resolveParams($service->params, $params);
		}
		if (isset($service->className)) {
			return new $service->className(...array_values($params));
		}
		return (object) $params;
    }

}
