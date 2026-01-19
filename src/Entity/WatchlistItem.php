<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'watchlist_item')]
class WatchlistItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private \DateTimeImmutable $addedAt;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $position = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $note = null;

    /**
     * @var list<string>
     */
    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $tags = null;

    #[ORM\Column(type: 'decimal', precision: 18, scale: 6, nullable: true)]
    private ?string $alertPriceAbove = null;

    #[ORM\Column(type: 'decimal', precision: 18, scale: 6, nullable: true)]
    private ?string $alertPriceBelow = null;

    #[ORM\ManyToOne(targetEntity: Watchlist::class, inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Watchlist $watchlist = null;

    #[ORM\ManyToOne(targetEntity: Stock::class, inversedBy: 'watchlistItems')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Stock $stock = null;

    public function __construct()
    {
        $this->addedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAddedAt(): \DateTimeImmutable
    {
        return $this->addedAt;
    }

    public function setAddedAt(\DateTimeImmutable $addedAt): self
    {
        $this->addedAt = $addedAt;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }

    /**
     * @return list<string>|null
     */
    public function getTags(): ?array
    {
        return $this->tags;
    }

    /**
     * @param list<string>|null $tags
     */
    public function setTags(?array $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    public function getAlertPriceAbove(): ?string
    {
        return $this->alertPriceAbove;
    }

    public function setAlertPriceAbove(?string $alertPriceAbove): self
    {
        $this->alertPriceAbove = $alertPriceAbove;

        return $this;
    }

    public function getAlertPriceBelow(): ?string
    {
        return $this->alertPriceBelow;
    }

    public function setAlertPriceBelow(?string $alertPriceBelow): self
    {
        $this->alertPriceBelow = $alertPriceBelow;

        return $this;
    }

    public function getWatchlist(): ?Watchlist
    {
        return $this->watchlist;
    }

    public function setWatchlist(?Watchlist $watchlist): self
    {
        $this->watchlist = $watchlist;

        return $this;
    }

    public function getStock(): ?Stock
    {
        return $this->stock;
    }

    public function setStock(?Stock $stock): self
    {
        $this->stock = $stock;

        return $this;
    }
}
