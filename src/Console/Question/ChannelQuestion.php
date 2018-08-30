<?php


namespace MarcW\Silence\Console\Question;

use Doctrine\ORM\EntityManagerInterface;
use MarcW\Silence\Entity\Channel;
use Symfony\Component\Console\Question\ChoiceQuestion;

class ChannelQuestion extends ChoiceQuestion
{
    public function __construct(string $question, EntityManagerInterface $entityManager, $default = null)
    {
        $channelRepository = $entityManager->getRepository(Channel::class);
        $channels = $channelRepository->findAll();

        if ($default instanceof Channel) {
            $default = $default->getId();
        }

        $choices = [];
        foreach ($channels as $channel) {
            $choices[$channel->getId()] = $channel;
        }

        parent::__construct($question, $choices, $default);

        $this->setNormalizer(function($value) use ($channels) {
            foreach ($channels as $channel) {
                if ($value === $channel->__toString() || $value === $channel->getId()) {
                    return $channel;
                }
            }
        });
    }
}
