<?php


namespace MarcW\Silence\EventListener;


use AudienceHero\Bundle\FileBundle\ETL\Extractor\AudioDurationExtractor;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use MarcW\Silence\Entity\Episode;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class AudioDurationEventListener implements EventSubscriber
{
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return string[]
     */
    public function getSubscribedEvents()
    {
        return [
            'prePersist',
            'preUpdate',
        ];
    }

    private function getDuration(Episode $episode)
    {
        $publicDir = $this->parameterBag->get('dir.public');
        $path = sprintf('%s%s', $publicDir, $episode->getFile());

        $extractor = new AudioDurationExtractor();
        $duration = $extractor->extract($path);
        if ($duration) {
            $episode->setDuration($duration);
        }
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        /** @var Episode $object */
        $object = $args->getEntity();
        if (!$object instanceof Episode) {
            return;
        }

        $this->getDuration($object);
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        /** @var Episode $object */
        $object = $args->getEntity();
        if (!$object instanceof Episode) {
            return;
        }

        $this->getDuration($object);
    }
}
