<?php


namespace MarcW\Podcast\Command;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use MarcW\Podcast\Entity\Channel;
use MarcW\RssWriter\Bridge\Symfony\Form\ChoiceList\Loader\ItunesCategoryChoiceLoader;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ChannelShowCommand extends ChannelCrudCommand
{
    protected function configure()
    {
        $this->setName('channel:show')
            ->setDescription('List channels')
            ->addArgument('id', InputArgument::REQUIRED, 'The id of the channel')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $id = $input->getArgument('id');
        $channel = $this->entityManager->getRepository(Channel::class)->find($id);
        if (!$channel) {
            throw new \RuntimeException("There is no channel with id '$id'");
        }

        dump($channel);
    }
}