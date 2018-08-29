<?php


namespace MarcW\Podcast\Command;


use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\EntityManagerInterface;
use MarcW\Podcast\Entity\Channel;
use MarcW\RssWriter\Bridge\Symfony\Form\ChoiceList\Loader\ItunesCategoryChoiceLoader;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\PropertyAccess\PropertyAccess;

abstract class EpisodeCrudCommand extends AbstractCommand
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function ask()
    {
        $this->askForChannel('channel');
        $this->askForString('title');
        $this->askForString('subtitle');
        $this->askForString('description');
        $this->askForString('author');
        $this->askForBool('itunesBlock');
        $this->askForBool('isExplicit');
        $this->askForAudioFile('file');
        $this->askForImageFile('artwork');
        $this->askForString('duration');
        $this->askForDateTime('publishedAt');
    }

    protected function askForChannel(string $property)
    {
        $pa = PropertyAccess::createPropertyAccessor();
        $channelRepository = $this->entityManager->getRepository(Channel::class);
        $channels = $channelRepository->findAll();

        $default = $pa->getValue($this->getSubject(), $property);
        if ($default instanceof Channel) {
            $default = $default->getId();
        }

        $choices = [];
        foreach ($channels as $channel) {
            $choices[$channel->getId()] = $channel;
        }

        $helper = $this->getHelper('question');

        $question = new ChoiceQuestion(ucfirst(Inflector::camelize($property)), $choices, $default);
        $question->setNormalizer(function($value) use ($channels) {
            foreach ($channels as $channel) {
                if ($value === $channel->__toString() || $value === $channel->getId()) {
                    return $channel;
                }
            }
        });
        $value = $helper->ask($this->getInput(), $this->getOutput(), $question);

        $pa->setValue($this->subject, $property, $choices[$value]);
    }
}