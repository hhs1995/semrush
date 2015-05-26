<?php
require_once "vendor/autoload.php";

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

$paths = array(__DIR__."/app/Entity/");

//echo __DIR__."/app/Entities/ ";die();

$isDevMode = true;

$dbParams = require(__DIR__ . '/config/db.php');

$config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode, null, null, false);

$entityManager = EntityManager::create($dbParams, $config);