<?php


namespace MarcW\Silence\Command;

use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Sluggable\Util\Urlizer;
use MarcW\RssWriter\Extension\Atom\AtomWriter;
use MarcW\RssWriter\Extension\Core\CoreWriter;
use MarcW\RssWriter\Extension\DublinCore\DublinCore;
use MarcW\RssWriter\Extension\DublinCore\DublinCoreWriter;
use MarcW\RssWriter\Extension\Itunes\ItunesWriter;
use MarcW\RssWriter\RssWriter;
use MarcW\Silence\Entity\Channel;
use MarcW\Silence\Rss\ChannelBuilder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class RssGenerateCommand extends AbstractCommand
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;
    /**
     * @var ChannelBuilder
     */
    private $channelBuilder;

    public function __construct(EntityManagerInterface $entityManager, ParameterBagInterface $parameterBag, ChannelBuilder $channelBuilder)
    {
        $this->entityManager = $entityManager;
        $this->channelBuilder = $channelBuilder;

        parent::__construct($parameterBag);
    }

    protected function configure()
    {
        $this->setName('rss:generate')
             ->setDescription('Generate all the RSS files')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $channels = $this->entityManager->getRepository(Channel::class)->findAll();

        $writers = [
            new AtomWriter(),
            new CoreWriter(),
            new ItunesWriter(),
        ];
        $writer = new RssWriter(null, $writers, true, '    ');
        foreach ($channels as $channel) {
            $xml = $writer->writeChannel($this->channelBuilder->fromPodcastChannel($channel, $channel->getEpisodes()));
            file_put_contents(sprintf('%s/%s.xml', $this->parameterBag->get('dir.public'), Urlizer::urlize($channel->getTitle())), $xml);
        }
    }
}
