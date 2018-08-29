<?php


namespace MarcW\Podcast\Command;

use MarcW\Podcast\Entity\Channel;
use MarcW\RssWriter\Bridge\Symfony\Form\ChoiceList\Loader\ItunesCategoryChoiceLoader;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NewChannelCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('channel:new')
            ->setDescription('Create a new channel')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $channel = new Channel();
        $this->setSubject($channel);
        $this->setInput($input);
        $this->setOutput($output);

        do {
            $this->askForString('title');
            $this->askForString('subtitle');
            $this->askForString('description');
            $this->askForString('copyright');
            $this->askForString('author');
            $this->askForBool('itunesBlock');
            $this->askForImageFile('artwork');

            $choices = new ItunesCategoryChoiceLoader();
            $this->askForChoice('category', $choices->loadChoiceList()->getChoices());
            $this->askForBool('isExplicit');
            $this->askForBool('isComplete');
            $this->askForString('itunesOwnerEmail');
            $this->askForString('itunesOwnerName');
        } while (!$this->validateSubject());
    }
}