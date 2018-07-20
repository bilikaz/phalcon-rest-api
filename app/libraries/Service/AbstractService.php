<?php
declare(strict_types=1);

namespace App\Library\Service;

abstract class AbstractService implements ServiceInterface
{

    private $resolvedServices = [];
    private $services = [];
    private $serviceManager = null;

    public function setServiceManager(Manager\ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    public function registerServices(array $services)
    {
        $this->services = $services;
    }

    public function resolveService(string $service)
    {
        if (!isset($this->resolvedServices[$service])) {
            if (!isset($this->services[$service])) {
                throw new Exception\ServiceNotFoundException($service . ' wasn\'t registered');
            }
            $this->resolvedServices[$service] = $this->serviceManager->resolve($this->services[$service]);
        }
        return $this->resolvedServices[$service];
    }

}
