<?php

  namespace App\Entity;

  use Doctrine\ORM\Mapping as ORM;

  #[ORM\Entity]
  #[ORM\Table(name: 'job_run')]
  #[ORM\Index(columns: ['job_id'])]
  #[ORM\Index(columns: ['instrument_id'])]
  #[ORM\Index(columns: ['status'])]
  #[ORM\Index(columns: ['type'])]
  class JobRun
  {
      #[ORM\Id]
      #[ORM\Column(length: 64)]
      private string $id;

      #[ORM\ManyToOne(targetEntity: Job::class, inversedBy: 'runs')]
      #[ORM\JoinColumn(name: 'job_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
      private Job $job;

      #[ORM\ManyToOne(targetEntity: Instrument::class, inversedBy: 'jobRuns')]
      #[ORM\JoinColumn(name: 'instrument_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
      private ?Instrument $instrument = null;

      #[ORM\Column(length: 50)]
      private string $type;

      #[ORM\Column(length: 20)]
      private string $status;

      #[ORM\Column(length: 50, nullable: true)]
      private ?string $source = null;

      #[ORM\Column(type: 'text', nullable: true)]
      private ?string $message = null;

      #[ORM\Column(type: 'json', nullable: true)]
      private ?array $meta = null;

      #[ORM\Column(nullable: true)]
      private ?\DateTimeImmutable $startedAt = null;

      #[ORM\Column(nullable: true)]
      private ?\DateTimeImmutable $endedAt = null;

      #[ORM\Column(nullable: true)]
      private ?int $durationMs = null;

      #[ORM\Column]
      private \DateTimeImmutable $createdAt;

      #[ORM\Column]
      private \DateTimeImmutable $updatedAt;

      public function __construct()
      {
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

      public function getJob(): Job
      {
          return $this->job;
      }

      public function setJob(Job $job): self
      {
          $this->job = $job;
          return $this;
      }

      public function getInstrument(): ?Instrument
      {
          return $this->instrument;
      }

      public function setInstrument(?Instrument $instrument): self
      {
          $this->instrument = $instrument;
          return $this;
      }

      public function getType(): string
      {
          return $this->type;
      }

      public function setType(string $type): self
      {
          $this->type = $type;
          return $this;
      }

      public function getStatus(): string
      {
          return $this->status;
      }

      public function setStatus(string $status): self
      {
          $this->status = $status;
          return $this;
      }

      public function getSource(): ?string
      {
          return $this->source;
      }

      public function setSource(?string $source): self
      {
          $this->source = $source;
          return $this;
      }

      public function getMessage(): ?string
      {
          return $this->message;
      }

      public function setMessage(?string $message): self
      {
          $this->message = $message;
          return $this;
      }

      public function getMeta(): ?array
      {
          return $this->meta;
      }

      public function setMeta(?array $meta): self
      {
          $this->meta = $meta;
          return $this;
      }

      public function getStartedAt(): ?\DateTimeImmutable
      {
          return $this->startedAt;
      }

      public function setStartedAt(?\DateTimeImmutable $startedAt): self
      {
          $this->startedAt = $startedAt;
          return $this;
      }

      public function getEndedAt(): ?\DateTimeImmutable
      {
          return $this->endedAt;
      }

      public function setEndedAt(?\DateTimeImmutable $endedAt): self
      {
          $this->endedAt = $endedAt;
          return $this;
      }

      public function getDurationMs(): ?int
      {
          return $this->durationMs;
      }

      public function setDurationMs(?int $durationMs): self
      {
          $this->durationMs = $durationMs;
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
  }
