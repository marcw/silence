<?php


namespace MarcW\Podcast\Command;

use MarcW\Podcast\Entity\Channel;
use MarcW\Podcast\Entity\Episode;
use MarcW\RssWriter\Bridge\Symfony\Form\ChoiceList\Loader\ItunesCategoryChoiceLoader;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NewEpisodeCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('episode:new')
            ->setDescription('Create a new episode')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $episode = new Episode();
        $this->setSubject($episode);
        $this->setInput($input);
        $this->setOutput($output);

        do {
            $this->askForString('title');
            $this->askForString('subtitle');
            $this->askForString('description');
            $this->askForString('author');
            $this->askForBool('itunesBlock');
            $this->askForBool('isExplicit');
            $this->askForString('copyright');
            $this->askForString('duration');
            $this->askForImageFile('artwork');
            $this->askForAudioFile('file');
            $this->askForString('publishedAt');
        } while (!$this->validateSubject());
    }
}