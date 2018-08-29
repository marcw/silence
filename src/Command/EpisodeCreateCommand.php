<?php


namespace MarcW\Podcast\Command;

use MarcW\Podcast\Entity\Channel;
use MarcW\Podcast\Entity\Episode;
use MarcW\RssWriter\Bridge\Symfony\Form\ChoiceList\Loader\ItunesCategoryChoiceLoader;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EpisodeCreateCommand extends EpisodeCrudCommand
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
            $this->ask();
        } while (!$this->validateSubject());

        $this->entityManager->persist($episode);
        $this->entityManager->flush();
    }
}