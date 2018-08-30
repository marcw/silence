<?php


namespace MarcW\Silence\Command;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use MarcW\Silence\Entity\Channel;
use MarcW\Silence\Entity\Episode;
use MarcW\RssWriter\Bridge\Symfony\Form\ChoiceList\Loader\ItunesCategoryChoiceLoader;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EpisodeListCommand extends EpisodeCrudCommand
{
    protected function configure()
    {
        $this->setName('episode:list')
             ->setDescription('List episodes')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $table = new Table($output);
        $table->setHeaders(['id', 'title']);

        $episodes = $this->entityManager->getRepository(Episode::class)->findAll();
        foreach ($episodes as $episode) {
            $table->addRow([$episode->getId(), $episode->getTitle()]);
        }

        $table->render();
    }
}
