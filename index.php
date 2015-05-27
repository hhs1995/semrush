<?php

require_once "vendor/autoload.php";

use Symfony\Component\ClassLoader\ApcClassLoader;
use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();

$loader->useIncludePath(true);

$loader->registerNamespaces(array(
    'Core' => __DIR__ . '/core',
    'App\\Classes' => __DIR__ . '/app/Classes',
    'App\\Entity' => __DIR__ . '/app/Entity',
));

$cachedLoader = new ApcClassLoader('loader', $loader);

$cachedLoader->register();

include_once "di.php";


$api = new App\Classes\UserAPI();

$api->sendPushNotification([2, 3, 1], 'Hello');

print_r($api->getLastRecipients());