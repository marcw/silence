<?php


namespace MarcW\Silence\Command;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use MarcW\Silence\Entity\Channel;
use MarcW\RssWriter\Bridge\Symfony\Form\ChoiceList\Loader\ItunesCategoryChoiceLoader;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ChannelEditCommand extends ChannelCrudCommand
{
    protected function configure()
    {
        $this->setName('channel:edit')
            ->setDescription('Create a new channel')
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

        $this->setSubject($channel);
        $this->setInput($input);
        $this->setOutput($output);

        do {
            $this->ask();
        } while (!$this->validateSubject());

        $this->entityManager->flush();
    }
}
