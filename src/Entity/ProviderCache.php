<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'provider_cache')]
#[ORM\Index(columns: ['instrument_id'])]
#[ORM\Index(columns: ['provider', 'endpoint'])]
#[ORM\Index(columns: ['fetched_at'])]
class ProviderCache
{
    #[ORM\Id]
    #[ORM\Column(length: 64)]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Instrument::class, inversedBy: 'providerCaches')]
    #[ORM\JoinColumn(name: 'instrument_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Instrument $instrument;

    #[ORM\Column(length: 32)]
    private string $provider;

    #[ORM\Column(length: 32)]
    private string $endpoint;

    #[ORM\Column(type: 'text')]
    private string $payloadJson;

    #[ORM\Column]
    private \DateTimeImmutable $fetchedAt;

    #[ORM\Column]
    private int $ttlSeconds;

    public function __construct()
    {
        $this->fetchedAt = new \DateTimeImmutable();
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

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    public function setEndpoint(string $endpoint): self
    {
        $this->endpoint = $endpoint;
        return $this;
    }

    public function getPayloadJson(): string
    {
        return $this->payloadJson;
    }

    public function setPayloadJson(string $payloadJson): self
    {
        $this->payloadJson = $payloadJson;
        return $this;
    }

    public function getFetchedAt(): \DateTimeImmutable
    {
        return $this->fetchedAt;
    }

    public function setFetchedAt(\DateTimeImmutable $fetchedAt): self
    {
        $this->fetchedAt = $fetchedAt;
        return $this;
    }

    public function getTtlSeconds(): int
    {
        return $this->ttlSeconds;
    }

    public function setTtlSeconds(int $ttlSeconds): self
    {
        $this->ttlSeconds = $ttlSeconds;
        return $this;
    }
}
