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

namespace MarcW\Silence\Entity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table()
 */
class Episode
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\Column(type="string", length=36, nullable=false)
     */
    private $id;

    /**
     * @var null|Channel
     * @ORM\ManyToOne(targetEntity="Channel", inversedBy="episodes")
     * @ORM\JoinColumn(name="channel_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     * @Assert\NotNull
     */
    private $channel;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     * @Assert\NotBlank()
     * @Assert\Url()
     */
    private $link;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\Length(max=255)
     * @Assert\NotBlank
     */
    private $title;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    private $subtitle;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=4000, nullable=true)
     * @Assert\Length(max=4000)
     */
    private $description;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    private $author;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $itunesBlock = false;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isExplicit = false;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $artwork;

    /**
     * @var null|string
     * @ORM\Column(type="integer", nullable=true)
     */
    private $duration;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank
     */
    private $file;

    /**
     * @var null|\DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $publishedAt;

    public function __construct()
    {
        $this->id = Uuid::uuid4()->toString();
        $this->publishedAt = new \DateTime();
    }

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

    public function setFile(string $file): void
    {
        $this->file = $file;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setAuthor(string $author): void
    {
        $this->author = $author;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setDuration(int $duration): void
    {
        $this->duration = $duration;
    }

    public function getDuration(): ?int
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

    public function setPublishedAt(?\DateTime $publishedAt): void
    {
        $this->publishedAt = $publishedAt;
    }

    public function getPublishedAt(): ?\DateTime
    {
        return $this->publishedAt;
    }

    /**
     * @return string
     */
    public function getArtwork(): ?string
    {
        return $this->artwork;
    }

    /**
     * @param string $artwork
     */
    public function setArtwork(string $artwork): void
    {
        $this->artwork = $artwork;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return null|string
     */
    public function getLink(): ?string
    {
        return $this->link;
    }

    /**
     * @param null|string $link
     */
    public function setLink(?string $link): void
    {
        $this->link = $link;
    }
}
