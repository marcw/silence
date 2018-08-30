<?php


namespace MarcW\Silence\Command;


use Doctrine\ORM\EntityManagerInterface;
use MarcW\RssWriter\Bridge\Symfony\Form\ChoiceList\Loader\ItunesCategoryChoiceLoader;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

abstract class ChannelCrudCommand extends AbstractCommand
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager, ParameterBagInterface $parameterBag)
    {
        $this->entityManager = $entityManager;

        parent::__construct($parameterBag);
    }

    protected function ask()
    {
        $this->askForString('Title', 'title');
        $this->askForString('Subtitle', 'subtitle');
        $this->askForString('Description', 'description');
        $this->askForString('Copyright', 'copyright');
        $this->askForString('Author', 'author');
        $this->askForBool('Block this channel from appearing on iTunes?', 'itunesBlock');
        $this->askForImageFile('artwork');

        $choices = new ItunesCategoryChoiceLoader();
        $this->askForChoice('iTunes Category', 'category', $choices->loadChoiceList()->getChoices());
        $this->askForLanguage('language');
        $this->askForBool('This channel has explicit language', 'isExplicit');
        $this->askForBool('This channel is complete (no more episodes will be published)', 'isComplete');
        $this->askForString('Channel owner email (for iTunes admninistrative purposes)', 'itunesOwnerEmail');
        $this->askForString('Channel owner name (for iTunes admninistrative purposes)', 'itunesOwnerName');
    }
}
