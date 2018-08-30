<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use MarcW\Silence\Command\ChannelCreateCommand;
use MarcW\Silence\Command\ChannelEditCommand;
use MarcW\Silence\Command\ChannelListCommand;
use MarcW\Silence\Command\ChannelShowCommand;
use MarcW\Silence\Command\EpisodeCreateCommand;
use MarcW\Silence\Command\EpisodeEditCommand;
use MarcW\Silence\Command\EpisodeListCommand;
use MarcW\Silence\Command\EpisodeShowCommand;
use MarcW\Silence\Command\RssGenerateCommand;
use MarcW\Silence\EventListener\AudioFileEventListener;
use MarcW\Silence\Rss\ChannelBuilder;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

$loader = require __DIR__.'/../vendor/autoload.php';

$container = new ContainerBuilder();

// Configure the database
$dbParams = [
    'driver'   => 'pdo_sqlite',
    'path' => __DIR__.'/../private/db.sqlite',
];

AnnotationRegistry::registerLoader([$loader, 'loadClass']);
$config = Setup::createAnnotationMetadataConfiguration([__DIR__.'/../src/Entity'], true, null, null, false);
$entityManager = EntityManager::create($dbParams, $config);

// Configure the container
$container->register('entity_manager', EntityManagerInterface::class)->setSynthetic(true)->setPublic(true);
$container->register('parameter_bag', ParameterBagInterface::class)->setSynthetic(true)->setPublic(true);
$container->setAlias(EntityManagerInterface::class, 'entity_manager');
$container->setAlias(ParameterBagInterface::class, 'parameter_bag');
$container->register(ChannelBuilder::class)->setAutoconfigured(true)->setAutowired(true)->setPublic(true);
$container->register(RssGenerateCommand::class)->setAutoconfigured(true)->setAutowired(true)->setPublic(true);
$container->register(ChannelCreateCommand::class)->setAutoconfigured(true)->setAutowired(true)->setPublic(true);
$container->register(ChannelEditCommand::class)->setAutoconfigured(true)->setAutowired(true)->setPublic(true);
$container->register(ChannelListCommand::class)->setAutoconfigured(true)->setAutowired(true)->setPublic(true);
$container->register(ChannelShowCommand::class)->setAutoconfigured(true)->setAutowired(true)->setPublic(true);
$container->register(EpisodeCreateCommand::class)->setAutoconfigured(true)->setAutowired(true)->setPublic(true);
$container->register(EpisodeEditCommand::class)->setAutoconfigured(true)->setAutowired(true)->setPublic(true);
$container->register(EpisodeListCommand::class)->setAutoconfigured(true)->setAutowired(true)->setPublic(true);
$container->register(EpisodeShowCommand::class)->setAutoconfigured(true)->setAutowired(true)->setPublic(true);
$container->register(AudioFileEventListener::class)->setAutoconfigured(true)->setAutowired(true)->setPublic(true);

$container->setParameter('dir.root', realpath(__DIR__ . '/../'));
$container->setParameter('dir.public', realpath(__DIR__ . '/../public'));
$container->setParameter('dir.private', realpath(__DIR__.'/../private'));
$container->setParameter('base_url', 'https://podcasts.polaarsounds.com');

// Compile the container
$container->compile();
$container->set('entity_manager', $entityManager);
$container->set('parameter_bag', $container->getParameterBag());
$entityManager->getEventManager()->addEventSubscriber($container->get(AudioFileEventListener::class));

$timestampableListener = new Gedmo\Timestampable\TimestampableListener();
$timestampableListener->setAnnotationReader(new \Doctrine\Common\Annotations\AnnotationReader());
$entityManager->getEventManager()->addEventSubscriber($timestampableListener);
