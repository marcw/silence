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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity()
 * @ORM\Table()
 */
class Channel
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\Column(type="string", length=36, nullable=false)
     */
    private $id;

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
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     * @Assert\NotBlank()
     * @Assert\Url()
     */
    private $link;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=4000, nullable=true)
     * @Assert\Length(max=4000)
     * @Assert\NotBlank
     */
    private $description;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    private $copyright;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="Episode", mappedBy="channel")
     * @ORM\OrderBy("publishedAt")
     */
    private $episodes;

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
     * @var null|string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $category;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isExplicit = false;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isComplete = false;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank()
     */
    private $artwork;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=8, nullable=false)
     * @Assert\NotBlank()
     */
    private $language;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     * @Assert\Email
     */
    private $itunesOwnerEmail;

    /**
     * @var null|string
     * @Assert\Length(max=255)
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $itunesOwnerName;

    public function __construct()
    {
        $this->episodes = new ArrayCollection();
        $this->id = Uuid::uuid4()->toString();
    }

    public function __toString(): string
    {
        return $this->title ?? '';
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setSubtitle(string $subtitle): void
    {
        $this->subtitle = $subtitle;
    }

    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getEpisodes(): Collection
    {
        return $this->episodes;
    }

    public function addEpisode(Episode $episode): void
    {
        $this->episodes[] = $episode;
    }

    public function setCopyright(?string $copyright): void
    {
        $this->copyright = $copyright;
    }

    public function getCopyright(): ?string
    {
        return $this->copyright;
    }

    public function setAuthor(?string $author): void
    {
        $this->author = $author;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setItunesBlock(bool $itunesBlock): void
    {
        $this->itunesBlock = $itunesBlock;
    }

    public function getItunesBlock(): bool
    {
        return $this->itunesBlock;
    }

    public function setCategory(string $category): void
    {
        $this->category = $category;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setIsExplicit(bool $isExplicit): void
    {
        $this->isExplicit = $isExplicit;
    }

    public function getIsExplicit(): bool
    {
        return $this->isExplicit;
    }

    public function setIsComplete(bool $isComplete): void
    {
        $this->isComplete = $isComplete;
    }

    public function getIsComplete(): bool
    {
        return $this->isComplete;
    }

    public function setItunesOwnerEmail(?string $itunesOwnerEmail): void
    {
        $this->itunesOwnerEmail = $itunesOwnerEmail;
    }

    public function getItunesOwnerEmail(): ?string
    {
        return $this->itunesOwnerEmail;
    }

    public function setItunesOwnerName(?string $itunesOwnerName): void
    {
        $this->itunesOwnerName = $itunesOwnerName;
    }

    public function getItunesOwnerName(): ?string
    {
        return $this->itunesOwnerName;
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context)
    {
        if ($this->artwork) {
            list ($width, $height) = getimagesize($this->artwork);
            if ($width < 1400) {
                $context
                    ->buildViolation('Image width should be at least 1400px')
                    ->atPath('artwork')
                    ->addViolation();

            }

            if ($height < 1400) {
                $context->buildViolation('Image height should be at least 1400px')
                    ->atPath('artwork')
                    ->addViolation();
            }
        }
    }

    public function setLanguage(string $language): void
    {
        $this->language = $language;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
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
