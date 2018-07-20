<?php
declare(strict_types=1);

use Phalcon\Loader,
    Phalcon\Config\Adapter\Yaml as Config;

use App\Library\Service\Manager\ServiceManager,
    App\Library\Api\Config as ApiConfig,
    App\Library\Api\Request as ApiRequest;

include(BASE_PATH . 'vendor/autoload.php');

$config = new Config(BASE_PATH . 'app/configs/config.yaml');
if (file_exists(BASE_PATH . 'app/configs/' . ENVIRONMENT . '.yaml')) {
    $tmp = new Config(BASE_PATH . 'app/configs/' . ENVIRONMENT . '.yaml');
    $config->merge($tmp);
}

$namespaces = [
    'App\Library' => BASE_PATH . 'app/libraries',
];
if ($config->application->modules) {
    foreach ($config->application->modules as $module) {
        $path = BASE_PATH . 'app/modules/' . ucfirst($module);
        if (file_exists($path . '/configs/' . ENVIRONMENT . '.yaml')) {
            $tmp = new Config($path . '/configs/' . ENVIRONMENT . '.yaml');
            $tmp->merge($config);
            $config = $tmp;
        }
        $tmp = new Config($path . '/configs/config.yaml');
        $tmp->merge($config);
        $config = $tmp;

        $namespaces[$config->modules->{$module}->namespace] = $path;
    }
}

date_default_timezone_set($config->application->timezone);

$loader = new Loader();
$loader->registerNamespaces($namespaces)->register();

$apiConfig = new ApiConfig();
$apiConfig->merge($config);
$di->setShared('config', $apiConfig);

$apiRequest = new ApiRequest();
$di->setShared('request', $apiRequest);

$serviceManager = new ServiceManager($di);
$di->setShared('serviceManager', $serviceManager);

if ($config->application->services) {
    foreach ($config->application->services as $alias => $service) {
        $serviceManager->register($alias, $service);
    }
}

if ($config->application->repositories) {
    foreach ($config->application->repositories as $repository => $className) {
        $di->setShared($repository, $className);
    }
}
