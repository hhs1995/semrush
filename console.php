<?php

require_once "vendor/autoload.php";

use Symfony\Component\ClassLoader\ApcClassLoader;
use Symfony\Component\ClassLoader\UniversalClassLoader;
use Console\Command\NotificationsWorkerCommand;
use Symfony\Component\Console\Application;

$loader = new UniversalClassLoader();

$loader->useIncludePath(true);

$loader->registerNamespaces(array(
    'Core' => __DIR__.'/core',
    'App\\Classes' => __DIR__.'/app/Classes',
    'App\\Entity' => __DIR__.'/app/Entity',
));

$cachedLoader = new ApcClassLoader('loader', $loader);

$cachedLoader->register();

include_once "di.php";

$application = new Application();
$application->add(new NotificationsWorkerCommand());
$application->run();