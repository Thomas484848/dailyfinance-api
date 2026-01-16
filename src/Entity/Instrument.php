<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'instrument')]
#[ORM\UniqueConstraint(columns: ['symbol', 'exchange_code'])]
#[ORM\Index(columns: ['symbol'])]
#[ORM\Index(columns: ['exchange_code'])]
#[ORM\Index(columns: ['country'])]
class Instrument
{
    #[ORM\Id]
    #[ORM\Column(length: 64)]
    private string $id;

    #[ORM\Column(length: 32)]
    private string $symbol;

    #[ORM\Column(length: 16, nullable: true)]
    private ?string $exchangeCode = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(length: 8, nullable: true)]
    private ?string $currency = null;

    #[ORM\Column(length: 128, nullable: true)]
    private ?string $sector = null;

    #[ORM\Column(length: 128, nullable: true)]
    private ?string $industry = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $isin = null;

    #[ORM\Column(options: ['default' => true])]
    private bool $active = true;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column]
    private \DateTimeImmutable $updatedAt;

    /**
     * @var Collection<int, SymbolAlias>
     */
    #[ORM\OneToMany(mappedBy: 'instrument', targetEntity: SymbolAlias::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $aliases;

    /**
     * @var Collection<int, Metrics>
     */
    #[ORM\OneToMany(mappedBy: 'instrument', targetEntity: Metrics::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $metrics;

    /**
     * @var Collection<int, ProviderCache>
     */
    #[ORM\OneToMany(mappedBy: 'instrument', targetEntity: ProviderCache::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $providerCaches;

    /**
     * @var Collection<int, JobRun>
     */
    #[ORM\OneToMany(mappedBy: 'instrument', targetEntity: JobRun::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $jobRuns;

    public function __construct()
    {
        $this->aliases = new ArrayCollection();
        $this->metrics = new ArrayCollection();
        $this->providerCaches = new ArrayCollection();
        $this->jobRuns = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    public function setSymbol(string $symbol): self
    {
        $this->symbol = $symbol;
        return $this;
    }

    public function getExchangeCode(): ?string
    {
        return $this->exchangeCode;
    }

    public function setExchangeCode(?string $exchangeCode): self
    {
        $this->exchangeCode = $exchangeCode;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;
        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    public function getSector(): ?string
    {
        return $this->sector;
    }

    public function setSector(?string $sector): self
    {
        $this->sector = $sector;
        return $this;
    }

    public function getIndustry(): ?string
    {
        return $this->industry;
    }

    public function setIndustry(?string $industry): self
    {
        $this->industry = $industry;
        return $this;
    }

    public function getIsin(): ?string
    {
        return $this->isin;
    }

    public function setIsin(?string $isin): self
    {
        $this->isin = $isin;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return Collection<int, SymbolAlias>
     */
    public function getAliases(): Collection
    {
        return $this->aliases;
    }

    /**
     * @return Collection<int, Metrics>
     */
    public function getMetrics(): Collection
    {
        return $this->metrics;
    }

    /**
     * @return Collection<int, ProviderCache>
     */
    public function getProviderCaches(): Collection
    {
        return $this->providerCaches;
    }

    /**
     * @return Collection<int, JobRun>
     */
    public function getJobRuns(): Collection
    {
        return $this->jobRuns;
    }
}
