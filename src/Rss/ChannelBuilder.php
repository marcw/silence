<?php

/*
 * This file is part of the Podcast project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace MarcW\Podcast\Rss;

use Doctrine\Common\Collections\Collection;
use MarcW\Podcast\Entity\Channel;
use MarcW\Podcast\Entity\Episode;
use MarcW\RssWriter\Extension\Atom\AtomLink;
use MarcW\RssWriter\Extension\Core\Channel as RssChannel;
use MarcW\RssWriter\Extension\Core\Enclosure;
use MarcW\RssWriter\Extension\Core\Guid;
use MarcW\RssWriter\Extension\Core\Item;
use MarcW\RssWriter\Extension\Itunes\ItunesChannel;
use MarcW\RssWriter\Extension\Itunes\ItunesItem;
use MarcW\RssWriter\Extension\Itunes\ItunesOwner;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ChannelBuilder
{
    /** @var UrlGeneratorInterface */
    private $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param Channel $podcast
     * @param iterable $episodes
     * @return RssChannel
     */
    public function fromPodcastChannel(Channel $podcast, iterable $episodes): RssChannel
    {
        $channel = new RssChannel();
        $channel->setTitle($podcast->getTitle());
        $channel->setDescription($podcast->getDescription());
        $channel->setLink($this->router->generate('podcast_channels_listen', ['id' => $podcast->getId()], UrlGeneratorInterface::ABSOLUTE_URL));
        $channel->setCopyright($podcast->getCopyright());
        $channel->setGenerator('https://www.audiencehero.org');
        $channel->setLastBuildDate(new \DateTime());
        $itunesChannel = new ItunesChannel();
        $itunesChannel->setSubtitle($podcast->getSubtitle());
        $itunesChannel->setSummary($podcast->getDescription());
        $itunesChannel->setAuthor($podcast->getAuthor());
        $itunesChannel->setBlock($podcast->getItunesBlock());
        $itunesChannel->setExplicit($podcast->getIsExplicit());
        $itunesChannel->setComplete($podcast->getIsComplete());
        if ($podcast->getItunesOwnerEmail() || $podcast->getItunesOwnerName()) {
            $itunesChannel->setOwner((new ItunesOwner())->setEmail($podcast->getItunesOwnerEmail())->setName($podcast->getItunesOwnerName()));
        }

        if ($podcast->getArtwork()) {
            $artwork = $podcast->getArtwork();
            $size = $artwork->getImageWidth() >= $artwork->getImageHeight() ? '0x1500' : '1500x0';
            $itunesChannel->setImage($this->router->generate('audience_hero_img_show_alt', [
                'url' => urlencode($artwork->getRemoteUrl()),
                'crop' => 'square-center',
                'size' => $size,
            ], UrlGeneratorInterface::ABSOLUTE_URL));
        }

        if ($podcast->getCategory()) {
            $itunesChannel->addCategory($podcast->getCategory());
        }

        /** @var Episode $episode */
        foreach ($episodes as $episode) {
            $item = new Item();
            $item->setTitle($episode->getTitle());
            $item->setLink($this->router->generate('podcast_episodes_listen', ['id' => $podcast->getId(), 'episodeId' => $episode->getId()], UrlGeneratorInterface::ABSOLUTE_URL));
            $item->setDescription($episode->getDescription());
            $item->setAuthor($episode->getAuthor());
            $enclosureUrl = $this->router->generate('podcast_episodes_enclosure', ['id' => $episode->getId(), 'extension' => $episode->getFile()->getExtension()], UrlGeneratorInterface::ABSOLUTE_URL);
            $item->setEnclosure((new Enclosure())->setUrl($enclosureUrl)->setLength($episode->getFile()->getSize())->setType($episode->getFile()->getContentType()));
            $item->setGuid((new Guid())->setIsPermaLink(true)->setGuid($item->getLink()));
            $item->setPubDate($episode->getPublishedAt());
            $itunesItem = new ItunesItem();
            $item->addExtension($itunesItem);
            $itunesItem->setAuthor($episode->getAuthor());
            $itunesItem->setBlock($episode->getItunesBlock());
            if ($episode->getArtwork()) {
                $artwork = $episode->getArtwork();
                $size = $artwork->getImageWidth() >= $artwork->getImageHeight() ? '0x1500' : '1500x0';
                $itunesItem->setImage($this->router->generate('audience_hero_img_show_alt', [
                    'url' => urlencode($artwork->getRemoteUrl()),
                    'crop' => 'square-center',
                    'size' => $size,
                ], UrlGeneratorInterface::ABSOLUTE_URL));
            }
            $itunesItem->setExplicit($episode->isExplicit());
            $itunesItem->setSubtitle($episode->getSubtitle());
            $itunesItem->setDuration(DurationConverter::toHumanReadable($episode->getFile()->getPublicMetadataValue('duration')));
            $itunesItem->setSummary($episode->getDescription());
            $channel->addItem($item);
        }
        $channel->setLanguage($podcast->getLanguage());
        $channel->addExtension($itunesChannel);
        $channel->addExtension((new AtomLink())->setRel('self')->setHref($this->router->generate('podcast_channels_feed', ['id' => $podcast->getId()], UrlGeneratorInterface::ABSOLUTE_URL)));
        return $channel;
    }
}