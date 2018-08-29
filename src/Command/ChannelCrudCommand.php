<?php


namespace MarcW\Podcast\Command;


use Doctrine\ORM\EntityManagerInterface;
use MarcW\RssWriter\Bridge\Symfony\Form\ChoiceList\Loader\ItunesCategoryChoiceLoader;

abstract class ChannelCrudCommand extends AbstractCommand
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
        $this->askForString('title');
        $this->askForString('subtitle');
        $this->askForString('description');
        $this->askForString('copyright');
        $this->askForString('author');
        $this->askForBool('itunesBlock');
        $this->askForImageFile('artwork');

        $choices = new ItunesCategoryChoiceLoader();
        $this->askForChoice('category', $choices->loadChoiceList()->getChoices());
        $this->askForLanguage('language');
        $this->askForBool('isExplicit');
        $this->askForBool('isComplete');
        $this->askForString('itunesOwnerEmail');
        $this->askForString('itunesOwnerName');
    }
}