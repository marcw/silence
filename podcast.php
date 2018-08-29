<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use MarcW\Podcast\Console\Application;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

$loader = require __DIR__.'/vendor/autoload.php';


$isDevMode = false;

$paths = array(__DIR__.'/src/Entity');

// the connection configuration
$dbParams = array(
//    'driver'   => 'pdo_mysql',
//    'user'     => 'root',
//    'password' => '',
//    'dbname'   => 'foo',
);

AnnotationRegistry::registerLoader([$loader, 'loadClass']);
//$config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
//$entityManager = EntityManager::create($dbParams, $config);

$application = new Application();

$application->add(new \MarcW\Podcast\Command\GenerateCommand());
$application->add(new \MarcW\Podcast\Command\NewChannelCommand());
$application->run();
