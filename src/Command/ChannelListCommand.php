<?php


namespace MarcW\Podcast\Command;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use MarcW\Podcast\Entity\Channel;
use MarcW\RssWriter\Bridge\Symfony\Form\ChoiceList\Loader\ItunesCategoryChoiceLoader;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ChannelListCommand extends ChannelCrudCommand
{
    protected function configure()
    {
        $this->setName('channel:list')
             ->setDescription('List channels')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $table = new Table($output);
        $table->setHeaders(['id', 'title']);

        $channels = $this->entityManager->getRepository(Channel::class)->findAll();
        foreach ($channels as $channel) {
            $table->addRow([$channel->getId(), $channel->getTitle()]);
        }

        $table->render();
    }
}