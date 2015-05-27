<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\HttpFoundation\Session\Session;

$cache = new \Doctrine\Common\Cache\ApcCache;

$builder = new \DI\ContainerBuilder();

$builder->setDefinitionCache($cache);

$container = $builder->build();

$container->set('entityManager',
    \DI\factory(function () use ($cache) {

        $paths = [
            __DIR__ . "/app/Entity"
        ];

        $dbParams = require(__DIR__ . '/config/db.php');

        $config = Setup::createConfiguration(false, null, $cache);

        $config->setQueryCacheImpl($cache);

        $config->setAutoGenerateProxyClasses(false);

        $driver = new AnnotationDriver(new AnnotationReader(), $paths);

        AnnotationRegistry::registerLoader('class_exists');

        $config->setEntityNamespaces([
            'Entity' => 'App\\Entity'
        ]);

        $config->setMetadataDriverImpl($driver);

        return EntityManager::create($dbParams, $config);

    })
);

$container->set('session',
    \DI\factory(function () {

        $session = new Session();

        $session->start();

        return $session;

    })
);

$container->set('cache',
    \DI\factory(function () {

        $memcached = new \Memcached();

        $memcached->addServer('127.0.0.1',11211);

        $cache = new \Doctrine\Common\Cache\MemcachedCache();

        $cache->setMemcached($memcached);

        return $cache;
    })
);

Core\App::setDi($container);