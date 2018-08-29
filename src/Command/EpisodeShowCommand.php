<?php


namespace MarcW\Podcast\Command;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use MarcW\Podcast\Entity\Channel;
use MarcW\Podcast\Entity\Episode;
use MarcW\RssWriter\Bridge\Symfony\Form\ChoiceList\Loader\ItunesCategoryChoiceLoader;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EpisodeShowCommand extends EpisodeCrudCommand
{
    protected function configure()
    {
        $this->setName('episode:show')
            ->setDescription('Show a specific episode')
            ->addArgument('id', InputArgument::REQUIRED, 'The id of the episode')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $id = $input->getArgument('id');
        $episode = $this->entityManager->getRepository(Episode::class)->find($id);
        if (!$episode) {
            throw new \RuntimeException("There is no episode with id '$id'");
        }

        dump($episode);
    }
}