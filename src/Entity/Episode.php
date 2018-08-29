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

namespace MarcW\Podcast\Entity;

/**
 * PodcastEpisode.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 *
 * @ORM\Entity(repositoryClass="AudienceHero\Bundle\PodcastBundle\Repository\PodcastEpisodeRepository")
 * @ORM\Table(name="ah_podcast_episode", indexes={@ORM\Index(columns={"slug"})})
 */
class Episode
{
    /**
     * @var null|Channel
     * @ORM\ManyToOne(targetEntity="Channel", inversedBy="episodes")
     * @ORM\JoinColumn(name="podcast_channel_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     * @Assert\NotNull
     * @MaxDepth(1)
     * @Groups({"read", "write"})
     */
    private $channel;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\Length(max=255)
     * @Groups({"read", "write"})
     * @Assert\NotBlank
     */
    private $title;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Gedmo\Slug(unique=true, unique_base="channel", updatable=true, separator="-", fields={"title"})
     * @Groups({"read"})
     */
    private $slug;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     * @Groups({"read", "write"})
     */
    private $subtitle;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=4000, nullable=true)
     * @Assert\Length(max=4000)
     * @Groups({"read", "write"})
     */
    private $description;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"read", "write"})
     * @Assert\Length(max=255)
     */
    private $author;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false)
     * @Groups({"read", "write"})
     */
    private $itunesBlock = false;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false)
     * @Groups({"read", "write"})
     */
    private $isExplicit = false;

    /**
     * @var null|File
     * @ORM\ManyToOne(targetEntity="AudienceHero\Bundle\FileBundle\Entity\File")
     * @ORM\JoinColumn(name="artwork_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     * @Groups({"read", "write"})
     */
    private $artwork;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"read", "write"})
     */
    private $duration;

    /**
     * @var null|File
     * @ORM\ManyToOne(targetEntity="AudienceHero\Bundle\FileBundle\Entity\File")
     * @ORM\JoinColumn(name="file_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     * @Assert\NotNull
     * @Groups({"read", "write"})
     */
    private $file;

    /**
     * @var null|\DateTime
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"read", "write"})
     */
    private $publishedAt;

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setChannel(Channel $channel): void
    {
        $this->channel = $channel;
    }

    public function getChannel(): ?Channel
    {
        return $this->channel;
    }

//    public function setFile(File $file): void
//    {
//        $this->file = $file;
//        $this->setDuration(DurationConverter::toHumanReadable($file->getPublicMetadataValue('duration')));
//    }

//    public function getFile(): ?File
//    {
//        return $this->file;
//    }

    public function setAuthor(string $author): void
    {
        $this->author = $author;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setDuration(string $duration): void
    {
        $this->duration = $duration;
    }

    public function getDuration(): ?string
    {
        return $this->duration;
    }

    public function setIsExplicit(bool $isExplicit): void
    {
        $this->isExplicit = $isExplicit;
    }

    public function isExplicit(): bool
    {
        return $this->isExplicit;
    }

    public function setSubtitle(string $subtitle): void
    {
        $this->subtitle = $subtitle;
    }

    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function setItunesBlock(bool $itunesBlock): void
    {
        $this->itunesBlock = $itunesBlock;
    }

    public function getItunesBlock(): bool
    {
        return $this->itunesBlock;
    }

//    public function setArtwork(?File $artwork): void
//    {
//        $this->artwork = $artwork;
//    }
//
//    public function getArtwork(): ?File
//    {
//        return $this->artwork;
//    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function setPublishedAt(\DateTime $publishedAt): void
    {
        $this->publishedAt = $publishedAt;
    }

    public function getPublishedAt(): ?\DateTime
    {
        return $this->publishedAt;
    }
}
