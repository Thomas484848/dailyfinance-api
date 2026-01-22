<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/stocks',
            normalizationContext: ['groups' => ['stock:read']]
        ),
        new \ApiPlatform\Metadata\Get(
            uriTemplate: '/stocks/{id}',
            normalizationContext: ['groups' => ['stock:read']]
        ),
    ],
    paginationEnabled: true
)]
#[ApiFilter(SearchFilter::class, properties: [
    'symbol' => 'partial',
    'name' => 'partial',
    'exchangeCode' => 'exact',
    'country' => 'exact',
    'sector' => 'partial',
    'industry' => 'partial',
    'currency' => 'exact',
])]
#[ApiFilter(RangeFilter::class, properties: [
    'lastPrice',
    'marketCap',
])]
#[ORM\Entity]
#[ORM\Table(name: 'stock')]
class Stock
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['stock:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 32)]
    #[Groups(['stock:read'])]
    private string $symbol = '';

    #[ORM\Column(length: 64, nullable: true)]
    #[Groups(['stock:read'])]
    private ?string $exchangeCode = null;

    #[ORM\Column(length: 64, nullable: true)]
    #[Groups(['stock:read'])]
    private ?string $mic = null;

    #[ORM\Column(length: 32, nullable: true)]
    #[Groups(['stock:read'])]
    private ?string $isin = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['stock:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 64, nullable: true)]
    #[Groups(['stock:read'])]
    private ?string $type = null;

    #[ORM\Column(length: 8, nullable: true)]
    #[Groups(['stock:read'])]
    private ?string $currency = null;

    #[ORM\Column(length: 64, nullable: true)]
    #[Groups(['stock:read'])]
    private ?string $country = null;

    #[ORM\Column(length: 128, nullable: true)]
    #[Groups(['stock:read'])]
    private ?string $sector = null;

    #[ORM\Column(length: 128, nullable: true)]
    #[Groups(['stock:read'])]
    private ?string $industry = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['stock:read'])]
    private ?string $website = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['stock:read'])]
    private ?string $description = null;

    #[ORM\Column(length: 512, nullable: true)]
    #[Groups(['stock:read'])]
    private ?string $logoUrl = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['stock:read'])]
    private ?float $lastPrice = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['stock:read'])]
    private ?float $open = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['stock:read'])]
    private ?float $high = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['stock:read'])]
    private ?float $low = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['stock:read'])]
    private ?float $prevClose = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['stock:read'])]
    private ?float $change = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['stock:read'])]
    private ?float $changePercent = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['stock:read'])]
    private ?float $avgVolume30d = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['stock:read'])]
    private ?float $marketCap = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['stock:read'])]
    private ?float $sharesOutstanding = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['stock:read'])]
    private ?float $floatShares = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    #[Groups(['stock:read'])]
    private ?\DateTimeImmutable $quoteTimestamp = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    #[Groups(['stock:read'])]
    private ?\DateTimeImmutable $lastUpdatedAt = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['stock:read'])]
    private ?float $beta = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['stock:read'])]
    private ?float $dividendYield = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['stock:read'])]
    private ?float $dividendRate = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['stock:read'])]
    private ?float $epsTtm = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['stock:read'])]
    private ?float $peTtm = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['stock:read'])]
    private ?float $pb = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['stock:read'])]
    private ?float $psTtm = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['stock:read'])]
    private ?float $evEbitda = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['stock:read'])]
    private ?float $week52High = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['stock:read'])]
    private ?float $week52Low = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['stock:read'])]
    private ?float $revenueTtm = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['stock:read'])]
    private ?float $revenueGrowthYoy = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['stock:read'])]
    private ?float $epsGrowthYoy = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['stock:read'])]
    private ?float $grossMargin = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['stock:read'])]
    private ?float $operatingMargin = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['stock:read'])]
    private ?float $netMargin = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['stock:read'])]
    private ?float $freeCashFlowTtm = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['stock:read'])]
    private ?float $ebitdaTtm = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['stock:read'])]
    private ?float $netDebt = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['stock:read'])]
    private ?float $netDebtEbitda = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['stock:read'])]
    private ?float $priceToFcf = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['stock:read'])]
    private ?float $priceToCashFlow = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['stock:read'])]
    private ?float $returnOnEquity = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['stock:read'])]
    private ?float $returnOnAssets = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['stock:read'])]
    private ?float $debtToEquity = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['stock:read'])]
    private ?float $forwardPe = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['stock:read'])]
    private ?float $forwardEps = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['stock:read'])]
    private ?float $pegRatio = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['stock:read'])]
    private ?float $valuationScore = null;

    #[ORM\Column(length: 32, nullable: true)]
    #[Groups(['stock:read'])]
    private ?string $valuationLabel = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['stock:read'])]
    private ?float $valuationConfidence = null;

    #[ORM\Column(type: 'json', nullable: true)]
    #[Groups(['stock:read'])]
    private ?array $valuationBreakdown = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['stock:read'])]
    private ?string $valuationExplainText = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    #[Groups(['stock:read'])]
    private ?\DateTimeImmutable $valuationUpdatedAt = null;

    #[ORM\Column]
    #[Groups(['stock:read'])]
    private bool $active = true;

    #[ORM\Column(length: 64, nullable: true)]
    #[Groups(['stock:read'])]
    private ?string $dataSource = null;

    /**
     * @var Collection<int, WatchlistItem>
     */
    #[ORM\OneToMany(mappedBy: 'stock', targetEntity: WatchlistItem::class, orphanRemoval: true)]
    private Collection $watchlistItems;

    public function __construct()
    {
        $this->watchlistItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getMic(): ?string
    {
        return $this->mic;
    }

    public function setMic(?string $mic): self
    {
        $this->mic = $mic;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

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

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

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

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): self
    {
        $this->website = $website;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getLogoUrl(): ?string
    {
        return $this->logoUrl;
    }

    public function setLogoUrl(?string $logoUrl): self
    {
        $this->logoUrl = $logoUrl;

        return $this;
    }

    public function getLastPrice(): ?float
    {
        return $this->lastPrice;
    }

    public function setLastPrice(?float $lastPrice): self
    {
        $this->lastPrice = $lastPrice;

        return $this;
    }

    public function getOpen(): ?float
    {
        return $this->open;
    }

    public function setOpen(?float $open): self
    {
        $this->open = $open;

        return $this;
    }

    public function getHigh(): ?float
    {
        return $this->high;
    }

    public function setHigh(?float $high): self
    {
        $this->high = $high;

        return $this;
    }

    public function getLow(): ?float
    {
        return $this->low;
    }

    public function setLow(?float $low): self
    {
        $this->low = $low;

        return $this;
    }

    public function getPrevClose(): ?float
    {
        return $this->prevClose;
    }

    public function setPrevClose(?float $prevClose): self
    {
        $this->prevClose = $prevClose;

        return $this;
    }

    public function getChange(): ?float
    {
        return $this->change;
    }

    public function setChange(?float $change): self
    {
        $this->change = $change;

        return $this;
    }

    public function getChangePercent(): ?float
    {
        return $this->changePercent;
    }

    public function setChangePercent(?float $changePercent): self
    {
        $this->changePercent = $changePercent;

        return $this;
    }

    public function getAvgVolume30d(): ?float
    {
        return $this->avgVolume30d;
    }

    public function setAvgVolume30d(?float $avgVolume30d): self
    {
        $this->avgVolume30d = $avgVolume30d;

        return $this;
    }

    public function getMarketCap(): ?float
    {
        return $this->marketCap;
    }

    public function setMarketCap(?float $marketCap): self
    {
        $this->marketCap = $marketCap;

        return $this;
    }

    public function getSharesOutstanding(): ?float
    {
        return $this->sharesOutstanding;
    }

    public function setSharesOutstanding(?float $sharesOutstanding): self
    {
        $this->sharesOutstanding = $sharesOutstanding;

        return $this;
    }

    public function getFloatShares(): ?float
    {
        return $this->floatShares;
    }

    public function setFloatShares(?float $floatShares): self
    {
        $this->floatShares = $floatShares;

        return $this;
    }

    public function getQuoteTimestamp(): ?\DateTimeImmutable
    {
        return $this->quoteTimestamp;
    }

    public function setQuoteTimestamp(?\DateTimeImmutable $quoteTimestamp): self
    {
        $this->quoteTimestamp = $quoteTimestamp;

        return $this;
    }

    public function getLastUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->lastUpdatedAt;
    }

    public function setLastUpdatedAt(?\DateTimeImmutable $lastUpdatedAt): self
    {
        $this->lastUpdatedAt = $lastUpdatedAt;

        return $this;
    }

    public function getBeta(): ?float
    {
        return $this->beta;
    }

    public function setBeta(?float $beta): self
    {
        $this->beta = $beta;

        return $this;
    }

    public function getDividendYield(): ?float
    {
        return $this->dividendYield;
    }

    public function setDividendYield(?float $dividendYield): self
    {
        $this->dividendYield = $dividendYield;

        return $this;
    }

    public function getDividendRate(): ?float
    {
        return $this->dividendRate;
    }

    public function setDividendRate(?float $dividendRate): self
    {
        $this->dividendRate = $dividendRate;

        return $this;
    }

    public function getEpsTtm(): ?float
    {
        return $this->epsTtm;
    }

    public function setEpsTtm(?float $epsTtm): self
    {
        $this->epsTtm = $epsTtm;

        return $this;
    }

    public function getPeTtm(): ?float
    {
        return $this->peTtm;
    }

    public function setPeTtm(?float $peTtm): self
    {
        $this->peTtm = $peTtm;

        return $this;
    }

    public function getPb(): ?float
    {
        return $this->pb;
    }

    public function setPb(?float $pb): self
    {
        $this->pb = $pb;

        return $this;
    }

    public function getPsTtm(): ?float
    {
        return $this->psTtm;
    }

    public function setPsTtm(?float $psTtm): self
    {
        $this->psTtm = $psTtm;

        return $this;
    }

    public function getEvEbitda(): ?float
    {
        return $this->evEbitda;
    }

    public function setEvEbitda(?float $evEbitda): self
    {
        $this->evEbitda = $evEbitda;

        return $this;
    }

    public function getWeek52High(): ?float
    {
        return $this->week52High;
    }

    public function setWeek52High(?float $week52High): self
    {
        $this->week52High = $week52High;

        return $this;
    }

    public function getWeek52Low(): ?float
    {
        return $this->week52Low;
    }

    public function setWeek52Low(?float $week52Low): self
    {
        $this->week52Low = $week52Low;

        return $this;
    }

    public function getRevenueTtm(): ?float
    {
        return $this->revenueTtm;
    }

    public function setRevenueTtm(?float $revenueTtm): self
    {
        $this->revenueTtm = $revenueTtm;

        return $this;
    }

    public function getRevenueGrowthYoy(): ?float
    {
        return $this->revenueGrowthYoy;
    }

    public function setRevenueGrowthYoy(?float $revenueGrowthYoy): self
    {
        $this->revenueGrowthYoy = $revenueGrowthYoy;

        return $this;
    }

    public function getEpsGrowthYoy(): ?float
    {
        return $this->epsGrowthYoy;
    }

    public function setEpsGrowthYoy(?float $epsGrowthYoy): self
    {
        $this->epsGrowthYoy = $epsGrowthYoy;

        return $this;
    }

    public function getGrossMargin(): ?float
    {
        return $this->grossMargin;
    }

    public function setGrossMargin(?float $grossMargin): self
    {
        $this->grossMargin = $grossMargin;

        return $this;
    }

    public function getOperatingMargin(): ?float
    {
        return $this->operatingMargin;
    }

    public function setOperatingMargin(?float $operatingMargin): self
    {
        $this->operatingMargin = $operatingMargin;

        return $this;
    }

    public function getNetMargin(): ?float
    {
        return $this->netMargin;
    }

    public function setNetMargin(?float $netMargin): self
    {
        $this->netMargin = $netMargin;

        return $this;
    }

    public function getFreeCashFlowTtm(): ?float
    {
        return $this->freeCashFlowTtm;
    }

    public function setFreeCashFlowTtm(?float $freeCashFlowTtm): self
    {
        $this->freeCashFlowTtm = $freeCashFlowTtm;

        return $this;
    }

    public function getEbitdaTtm(): ?float
    {
        return $this->ebitdaTtm;
    }

    public function setEbitdaTtm(?float $ebitdaTtm): self
    {
        $this->ebitdaTtm = $ebitdaTtm;

        return $this;
    }

    public function getNetDebt(): ?float
    {
        return $this->netDebt;
    }

    public function setNetDebt(?float $netDebt): self
    {
        $this->netDebt = $netDebt;

        return $this;
    }

    public function getNetDebtEbitda(): ?float
    {
        return $this->netDebtEbitda;
    }

    public function setNetDebtEbitda(?float $netDebtEbitda): self
    {
        $this->netDebtEbitda = $netDebtEbitda;

        return $this;
    }

    public function getPriceToFcf(): ?float
    {
        return $this->priceToFcf;
    }

    public function setPriceToFcf(?float $priceToFcf): self
    {
        $this->priceToFcf = $priceToFcf;

        return $this;
    }

    public function getPriceToCashFlow(): ?float
    {
        return $this->priceToCashFlow;
    }

    public function setPriceToCashFlow(?float $priceToCashFlow): self
    {
        $this->priceToCashFlow = $priceToCashFlow;

        return $this;
    }

    public function getReturnOnEquity(): ?float
    {
        return $this->returnOnEquity;
    }

    public function setReturnOnEquity(?float $returnOnEquity): self
    {
        $this->returnOnEquity = $returnOnEquity;

        return $this;
    }

    public function getReturnOnAssets(): ?float
    {
        return $this->returnOnAssets;
    }

    public function setReturnOnAssets(?float $returnOnAssets): self
    {
        $this->returnOnAssets = $returnOnAssets;

        return $this;
    }

    public function getDebtToEquity(): ?float
    {
        return $this->debtToEquity;
    }

    public function setDebtToEquity(?float $debtToEquity): self
    {
        $this->debtToEquity = $debtToEquity;

        return $this;
    }

    public function getForwardPe(): ?float
    {
        return $this->forwardPe;
    }

    public function setForwardPe(?float $forwardPe): self
    {
        $this->forwardPe = $forwardPe;

        return $this;
    }

    public function getForwardEps(): ?float
    {
        return $this->forwardEps;
    }

    public function setForwardEps(?float $forwardEps): self
    {
        $this->forwardEps = $forwardEps;

        return $this;
    }

    public function getPegRatio(): ?float
    {
        return $this->pegRatio;
    }

    public function setPegRatio(?float $pegRatio): self
    {
        $this->pegRatio = $pegRatio;

        return $this;
    }

    public function getValuationScore(): ?float
    {
        return $this->valuationScore;
    }

    public function setValuationScore(?float $valuationScore): self
    {
        $this->valuationScore = $valuationScore;

        return $this;
    }

    public function getValuationLabel(): ?string
    {
        return $this->valuationLabel;
    }

    public function setValuationLabel(?string $valuationLabel): self
    {
        $this->valuationLabel = $valuationLabel;

        return $this;
    }

    public function getValuationConfidence(): ?float
    {
        return $this->valuationConfidence;
    }

    public function setValuationConfidence(?float $valuationConfidence): self
    {
        $this->valuationConfidence = $valuationConfidence;

        return $this;
    }

    public function getValuationBreakdown(): ?array
    {
        return $this->valuationBreakdown;
    }

    public function setValuationBreakdown(?array $valuationBreakdown): self
    {
        $this->valuationBreakdown = $valuationBreakdown;

        return $this;
    }

    public function getValuationExplainText(): ?string
    {
        return $this->valuationExplainText;
    }

    public function setValuationExplainText(?string $valuationExplainText): self
    {
        $this->valuationExplainText = $valuationExplainText;

        return $this;
    }

    public function getValuationUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->valuationUpdatedAt;
    }

    public function setValuationUpdatedAt(?\DateTimeImmutable $valuationUpdatedAt): self
    {
        $this->valuationUpdatedAt = $valuationUpdatedAt;

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

    public function getDataSource(): ?string
    {
        return $this->dataSource;
    }

    public function setDataSource(?string $dataSource): self
    {
        $this->dataSource = $dataSource;

        return $this;
    }

    /**
     * @return Collection<int, WatchlistItem>
     */
    public function getWatchlistItems(): Collection
    {
        return $this->watchlistItems;
    }
}
