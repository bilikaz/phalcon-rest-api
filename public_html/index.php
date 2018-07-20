<?php
declare(strict_types=1);

use Phalcon\DI\FactoryDefault,
    Phalcon\Events\Manager as EventsManager,
    Phalcon\Mvc\Micro as Application;
//    Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;

use Library\Response\Handler as ResponseHandler,
    Library\Resolver;

$_GET['_url'] = preg_replace('/\?.*/', '', $_SERVER['REQUEST_URI']);
error_reporting(E_ALL);
ini_set('display_errors', '1');

define('BASE_PATH', realpath(__DIR__.'/..') . '/');
define('WEB_PATH', realpath(__DIR__.'/.') . '/');

if (isset($_SERVER['APPLICATION_ENV']) && in_array($_SERVER['APPLICATION_ENV'], ['development', 'testing', 'production'])) {
    define('ENVIRONMENT', $_SERVER['APPLICATION_ENV']);
} else {
    define('ENVIRONMENT', 'production');
}

$di = new FactoryDefault();
$application = new Application($di);
$di->setShared('application', $application);

include(BASE_PATH . 'app/bootstrap.php');

if ($config->application->debug) {
    ini_set('display_errors', '1');
    $debug = new \Phalcon\Debug();
    $debug->listen();
}

$eventsManager = new EventsManager();
$di->setShared('eventsManager', $eventsManager);
$application->setEventsManager($eventsManager);

if ($config->application->eventsManagers) {
    foreach ($config->application->eventsManagers as $alias => $manager) {
        $manager = $serviceManager->resolve($manager);
        $eventsManager->attach('micro', $manager);
        $di->setShared($alias, $manager);
    }
}

$application->handle();
