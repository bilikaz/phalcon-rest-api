<?php
declare(strict_types=1);

namespace App\Library\Service;

interface ServiceInterface
{

    public function setServiceManager(Manager\ServiceManager $serviceManager);

    public function registerServices(array $services);

    public function resolveService(string $service);

}
