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

namespace MarcW\Silence\Rss;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Inflector\Inflector;
use Gedmo\Mapping\Annotation\Slug;
use Gedmo\Sluggable\Util\Urlizer;
use MarcW\Silence\Entity\Channel;
use MarcW\Silence\Entity\Episode;
use MarcW\RssWriter\Extension\Atom\AtomLink;
use MarcW\RssWriter\Extension\Core\Channel as RssChannel;
use MarcW\RssWriter\Extension\Core\Enclosure;
use MarcW\RssWriter\Extension\Core\Guid;
use MarcW\RssWriter\Extension\Core\Item;
use MarcW\RssWriter\Extension\Itunes\ItunesChannel;
use MarcW\RssWriter\Extension\Itunes\ItunesItem;
use MarcW\RssWriter\Extension\Itunes\ItunesOwner;
use MarcW\Silence\Util\DurationConverter;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ChannelBuilder
{
    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->baseUrl = $parameterBag->get('base_url');
        $this->publicDir = $parameterBag->get('dir.public');
    }

    private function generateUrl($path): string
    {
        return sprintf('%s/%s', rtrim($this->baseUrl, '/'), ltrim($path, '/'));
    }

    private function getFilePath($path): string
    {
        return sprintf('%s/%s', rtrim($this->publicDir, '/'), ltrim($path, '/'));
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
        $channel->setLink($podcast->getLink());
        $channel->setCopyright($podcast->getCopyright());
        $channel->setGenerator('https://github.com/marcw/silence');
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
            $itunesChannel->setImage($this->generateUrl($podcast->getArtwork()));
        }

        if ($podcast->getCategory()) {
            $itunesChannel->addCategory($podcast->getCategory());
        }

        /** @var Episode $episode */
        foreach ($episodes as $episode) {
            $item = new Item();
            $item->setTitle($episode->getTitle());
            $item->setLink($episode->getLink());
            $item->setDescription($episode->getDescription());
            $item->setAuthor($episode->getAuthor());
            $enclosureUrl = $this->generateUrl($episode->getFile());

            $item->setEnclosure((new Enclosure())
                 ->setUrl($enclosureUrl)
                 ->setLength($episode->getFilesize())
                 ->setType($episode->getFiletype()))
            ;

            $item->setGuid((new Guid())->setIsPermaLink(true)->setGuid($item->getLink()));
            $item->setPubDate($episode->getPublishedAt());
            $itunesItem = new ItunesItem();
            $item->addExtension($itunesItem);
            $itunesItem->setAuthor($episode->getAuthor());
            $itunesItem->setBlock($episode->getItunesBlock());
            if ($episode->getArtwork()) {
                $itunesItem->setImage($this->generateUrl($episode->getArtwork()));
            }
            $itunesItem->setExplicit($episode->isExplicit());
            $itunesItem->setSubtitle($episode->getSubtitle());
            $itunesItem->setDuration(DurationConverter::toHumanReadable($episode->getDuration()));
            $itunesItem->setSummary($episode->getDescription());
            $channel->addItem($item);
        }
        $channel->setLanguage($podcast->getLanguage());
        $channel->addExtension($itunesChannel);

        $channel->addExtension((new AtomLink())->setRel('self')->setHref(
            $this->generateUrl(sprintf('%s.rss', Urlizer::urlize($podcast->getTitle()))))
        );

        return $channel;
    }
}
