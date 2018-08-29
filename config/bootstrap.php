<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use MarcW\Podcast\Command\ChannelCreateCommand;
use MarcW\Podcast\Command\ChannelEditCommand;
use MarcW\Podcast\Command\ChannelListCommand;
use MarcW\Podcast\Command\ChannelShowCommand;
use MarcW\Podcast\Command\EpisodeCreateCommand;
use MarcW\Podcast\Command\EpisodeEditCommand;
use MarcW\Podcast\Command\EpisodeListCommand;
use MarcW\Podcast\Command\EpisodeShowCommand;
use Symfony\Component\DependencyInjection\ContainerBuilder;

$loader = require __DIR__.'/../vendor/autoload.php';

$container = new ContainerBuilder();

$dbParams = [
    'driver'   => 'pdo_sqlite',
    'path' => __DIR__.'/../private/db.sqlite',
];

AnnotationRegistry::registerLoader([$loader, 'loadClass']);
$config = Setup::createAnnotationMetadataConfiguration([__DIR__.'/../src/Entity'], true, null, null, false);
$entityManager = EntityManager::create($dbParams, $config);

$container->register('entity_manager', EntityManagerInterface::class)->setSynthetic(true);
$container->setAlias(EntityManagerInterface::class, 'entity_manager');
$container->register(ChannelCreateCommand::class)->setAutoconfigured(true)->setAutowired(true)->setPublic(true);
$container->register(ChannelEditCommand::class)->setAutoconfigured(true)->setAutowired(true)->setPublic(true);
$container->register(ChannelListCommand::class)->setAutoconfigured(true)->setAutowired(true)->setPublic(true);
$container->register(ChannelShowCommand::class)->setAutoconfigured(true)->setAutowired(true)->setPublic(true);
$container->register(EpisodeCreateCommand::class)->setAutoconfigured(true)->setAutowired(true)->setPublic(true);
$container->register(EpisodeEditCommand::class)->setAutoconfigured(true)->setAutowired(true)->setPublic(true);
$container->register(EpisodeListCommand::class)->setAutoconfigured(true)->setAutowired(true)->setPublic(true);
$container->register(EpisodeShowCommand::class)->setAutoconfigured(true)->setAutowired(true)->setPublic(true);
$container->compile();
$container->set('entity_manager', $entityManager);
