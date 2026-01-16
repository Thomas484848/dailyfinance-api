<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'symbol_alias')]
#[ORM\UniqueConstraint(columns: ['provider', 'symbol', 'exchange_code'])]
#[ORM\Index(columns: ['instrument_id'])]
#[ORM\Index(columns: ['provider'])]
class SymbolAlias
{
    #[ORM\Id]
    #[ORM\Column(length: 64)]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Instrument::class, inversedBy: 'aliases')]
    #[ORM\JoinColumn(name: 'instrument_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Instrument $instrument;

    #[ORM\Column(length: 32)]
    private string $provider;

    #[ORM\Column(length: 32)]
    private string $symbol;

    #[ORM\Column(length: 16, nullable: true)]
    private ?string $exchangeCode = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
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

    public function getInstrument(): Instrument
    {
        return $this->instrument;
    }

    public function setInstrument(Instrument $instrument): self
    {
        $this->instrument = $instrument;
        return $this;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function setProvider(string $provider): self
    {
        $this->provider = $provider;
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

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}
