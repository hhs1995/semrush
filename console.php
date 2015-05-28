<?php

require_once "vendor/autoload.php";

use Symfony\Component\ClassLoader\ApcClassLoader;
use Symfony\Component\ClassLoader\UniversalClassLoader;
use Console\Command\NotificationsWorkerCombineCommand;
use Console\Command\NotificationsWorkerPushCommand;
use Symfony\Component\Console\Application;

$loader = new UniversalClassLoader();

$loader->useIncludePath(true);

$loader->registerNamespaces(array(
    'Core' => __DIR__ . '/core',
    'App\\Classes' => __DIR__ . '/app/Classes',
    'App\\Entity' => __DIR__ . '/app/Entity',
));

$cachedLoader = new ApcClassLoader('loader', $loader);

$cachedLoader->register();

Core\App::setParams(require(__DIR__ . '/config/params.php'));

include_once "di.php";

$application = new Application();
$application->add(new NotificationsWorkerCombineCommand());
$application->add(new NotificationsWorkerPushCommand());
$application->run();