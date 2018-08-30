<?php


namespace MarcW\Silence\Command;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use MarcW\Silence\Entity\Channel;
use MarcW\RssWriter\Bridge\Symfony\Form\ChoiceList\Loader\ItunesCategoryChoiceLoader;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ChannelCreateCommand extends ChannelCrudCommand
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
            $this->ask();
        } while (!$this->validateSubject());

        $this->entityManager->persist($channel);
        $this->entityManager->flush();
    }
}
