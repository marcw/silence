<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

$loader = require __DIR__.'/../vendor/autoload.php';

$dbParams = [
    'driver'   => 'pdo_sqlite',
    'path' => __DIR__.'/../private/db.sqlite',
];

AnnotationRegistry::registerLoader([$loader, 'loadClass']);
$config = Setup::createAnnotationMetadataConfiguration([__DIR__.'/../src/Entity'], true, null, null, false);
$entityManager = EntityManager::create($dbParams, $config);
