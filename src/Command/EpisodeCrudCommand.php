<?php


namespace MarcW\Silence\Command;


use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\EntityManagerInterface;
use MarcW\Silence\Console\Question\ChannelQuestion;
use MarcW\Silence\Entity\Channel;
use MarcW\RssWriter\Bridge\Symfony\Form\ChoiceList\Loader\ItunesCategoryChoiceLoader;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

abstract class EpisodeCrudCommand extends AbstractCommand
{
    protected function ask()
    {
        $this->askForChannel('channel');
        $this->askForString('Title', 'title');
        $this->askForString('Subtitle', 'subtitle');
        $this->askForString('Link to the episode homepage', 'link');
        $this->askForString('Description', 'description');
        $this->askForString('Author', 'author');
        $this->askForBool('Block this episode from appearing on iTunes', 'itunesBlock');
        $this->askForBool('This episode has explicit language', 'isExplicit');
        $this->askForAudioFile('file');
        $this->askForImageFile('artwork');
        $this->askForDateTime('Date and time of publication (Y-m-d H:i:s)', 'publishedAt');
    }

    protected function askForChannel(string $property)
    {
        $default = $this->propertyAccessor->getValue($this->getSubject(), $property);
        $value = $this->askQuestion(new ChannelQuestion('Select the channel of this episode', $this->entityManager, $default));

        $this->propertyAccessor->setValue($this->subject, $property, $this->entityManager->getRepository(Channel::class)->find($value));
    }
}
