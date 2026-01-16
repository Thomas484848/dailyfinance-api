<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'metrics')]
#[ORM\Index(columns: ['instrument_id'])]
#[ORM\Index(columns: ['as_of_date'])]
class Metrics
{
    #[ORM\Id]
    #[ORM\Column(length: 64)]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Instrument::class, inversedBy: 'metrics')]
    #[ORM\JoinColumn(name: 'instrument_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Instrument $instrument;

    #[ORM\Column]
    private \DateTimeImmutable $asOfDate;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $price = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $open = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $high = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $low = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $close = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $volume = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $marketCap = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $pe = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $eps = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $dividendYield = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $revenueTtm = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $netIncomeTtm = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $grossMargin = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $operatingMargin = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $profitMargin = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $dividendPerShare = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $payoutRatio = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $revenuePerShare = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $epsDiluted = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $sharesOutstanding = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $floatShares = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $beta = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $week52High = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $week52Low = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $avgVolume = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $enterpriseValue = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $ebitdaTtm = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $freeCashFlowTtm = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $operatingCashFlowTtm = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $grossProfitTtm = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $totalDebt = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $totalCash = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $debtToEquity = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $currentRatio = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $quickRatio = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $priceToBook = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $priceToSales = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $pegRatio = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $evToEbitda = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $evToRevenue = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $bookValuePerShare = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $roa = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $roe = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $roi = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $priceSource = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $openSource = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $highSource = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $lowSource = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $closeSource = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $volumeSource = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $marketCapSource = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $peSource = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $epsSource = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $dividendYieldSource = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $revenueTtmSource = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $netIncomeTtmSource = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $grossMarginSource = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $operatingMarginSource = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $profitMarginSource = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $dividendPerShareSource = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $payoutRatioSource = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $revenuePerShareSource = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $epsDilutedSource = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $sharesOutstandingSource = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $floatSharesSource = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $betaSource = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $week52HighSource = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $week52LowSource = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $avgVolumeSource = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $enterpriseValueSource = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $ebitdaTtmSource = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $freeCashFlowTtmSource = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $operatingCashFlowTtmSource = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $grossProfitTtmSource = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $totalDebtSource = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $totalCashSource = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $debtToEquitySource = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $currentRatioSource = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $quickRatioSource = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $priceToBookSource = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $priceToSalesSource = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $pegRatioSource = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $evToEbitdaSource = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $evToRevenueSource = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $bookValuePerShareSource = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $roaSource = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $roeSource = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $roiSource = null;

    public function __construct()
    {
        $this->asOfDate = new \DateTimeImmutable();
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

    public function getAsOfDate(): \DateTimeImmutable
    {
        return $this->asOfDate;
    }

    public function setAsOfDate(\DateTimeImmutable $asOfDate): self
    {
        $this->asOfDate = $asOfDate;
        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;
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

    public function getClose(): ?float
    {
        return $this->close;
    }

    public function setClose(?float $close): self
    {
        $this->close = $close;
        return $this;
    }

    public function getVolume(): ?float
    {
        return $this->volume;
    }

    public function setVolume(?float $volume): self
    {
        $this->volume = $volume;
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

    public function getPe(): ?float
    {
        return $this->pe;
    }

    public function setPe(?float $pe): self
    {
        $this->pe = $pe;
        return $this;
    }

    public function getEps(): ?float
    {
        return $this->eps;
    }

    public function setEps(?float $eps): self
    {
        $this->eps = $eps;
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

    public function getRevenueTtm(): ?float
    {
        return $this->revenueTtm;
    }

    public function setRevenueTtm(?float $revenueTtm): self
    {
        $this->revenueTtm = $revenueTtm;
        return $this;
    }

    public function getNetIncomeTtm(): ?float
    {
        return $this->netIncomeTtm;
    }

    public function setNetIncomeTtm(?float $netIncomeTtm): self
    {
        $this->netIncomeTtm = $netIncomeTtm;
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

    public function getProfitMargin(): ?float
    {
        return $this->profitMargin;
    }

    public function setProfitMargin(?float $profitMargin): self
    {
        $this->profitMargin = $profitMargin;
        return $this;
    }

    public function getDividendPerShare(): ?float
    {
        return $this->dividendPerShare;
    }

    public function setDividendPerShare(?float $dividendPerShare): self
    {
        $this->dividendPerShare = $dividendPerShare;
        return $this;
    }

    public function getPayoutRatio(): ?float
    {
        return $this->payoutRatio;
    }

    public function setPayoutRatio(?float $payoutRatio): self
    {
        $this->payoutRatio = $payoutRatio;
        return $this;
    }

    public function getRevenuePerShare(): ?float
    {
        return $this->revenuePerShare;
    }

    public function setRevenuePerShare(?float $revenuePerShare): self
    {
        $this->revenuePerShare = $revenuePerShare;
        return $this;
    }

    public function getEpsDiluted(): ?float
    {
        return $this->epsDiluted;
    }

    public function setEpsDiluted(?float $epsDiluted): self
    {
        $this->epsDiluted = $epsDiluted;
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

    public function getBeta(): ?float
    {
        return $this->beta;
    }

    public function setBeta(?float $beta): self
    {
        $this->beta = $beta;
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

    public function getAvgVolume(): ?float
    {
        return $this->avgVolume;
    }

    public function setAvgVolume(?float $avgVolume): self
    {
        $this->avgVolume = $avgVolume;
        return $this;
    }

    public function getEnterpriseValue(): ?float
    {
        return $this->enterpriseValue;
    }

    public function setEnterpriseValue(?float $enterpriseValue): self
    {
        $this->enterpriseValue = $enterpriseValue;
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

    public function getFreeCashFlowTtm(): ?float
    {
        return $this->freeCashFlowTtm;
    }

    public function setFreeCashFlowTtm(?float $freeCashFlowTtm): self
    {
        $this->freeCashFlowTtm = $freeCashFlowTtm;
        return $this;
    }

    public function getOperatingCashFlowTtm(): ?float
    {
        return $this->operatingCashFlowTtm;
    }

    public function setOperatingCashFlowTtm(?float $operatingCashFlowTtm): self
    {
        $this->operatingCashFlowTtm = $operatingCashFlowTtm;
        return $this;
    }

    public function getGrossProfitTtm(): ?float
    {
        return $this->grossProfitTtm;
    }

    public function setGrossProfitTtm(?float $grossProfitTtm): self
    {
        $this->grossProfitTtm = $grossProfitTtm;
        return $this;
    }

    public function getTotalDebt(): ?float
    {
        return $this->totalDebt;
    }

    public function setTotalDebt(?float $totalDebt): self
    {
        $this->totalDebt = $totalDebt;
        return $this;
    }

    public function getTotalCash(): ?float
    {
        return $this->totalCash;
    }

    public function setTotalCash(?float $totalCash): self
    {
        $this->totalCash = $totalCash;
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

    public function getCurrentRatio(): ?float
    {
        return $this->currentRatio;
    }

    public function setCurrentRatio(?float $currentRatio): self
    {
        $this->currentRatio = $currentRatio;
        return $this;
    }

    public function getQuickRatio(): ?float
    {
        return $this->quickRatio;
    }

    public function setQuickRatio(?float $quickRatio): self
    {
        $this->quickRatio = $quickRatio;
        return $this;
    }

    public function getPriceToBook(): ?float
    {
        return $this->priceToBook;
    }

    public function setPriceToBook(?float $priceToBook): self
    {
        $this->priceToBook = $priceToBook;
        return $this;
    }

    public function getPriceToSales(): ?float
    {
        return $this->priceToSales;
    }

    public function setPriceToSales(?float $priceToSales): self
    {
        $this->priceToSales = $priceToSales;
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

    public function getEvToEbitda(): ?float
    {
        return $this->evToEbitda;
    }

    public function setEvToEbitda(?float $evToEbitda): self
    {
        $this->evToEbitda = $evToEbitda;
        return $this;
    }

    public function getEvToRevenue(): ?float
    {
        return $this->evToRevenue;
    }

    public function setEvToRevenue(?float $evToRevenue): self
    {
        $this->evToRevenue = $evToRevenue;
        return $this;
    }

    public function getBookValuePerShare(): ?float
    {
        return $this->bookValuePerShare;
    }

    public function setBookValuePerShare(?float $bookValuePerShare): self
    {
        $this->bookValuePerShare = $bookValuePerShare;
        return $this;
    }

    public function getRoa(): ?float
    {
        return $this->roa;
    }

    public function setRoa(?float $roa): self
    {
        $this->roa = $roa;
        return $this;
    }

    public function getRoe(): ?float
    {
        return $this->roe;
    }

    public function setRoe(?float $roe): self
    {
        $this->roe = $roe;
        return $this;
    }

    public function getRoi(): ?float
    {
        return $this->roi;
    }

    public function setRoi(?float $roi): self
    {
        $this->roi = $roi;
        return $this;
    }

    public function getPriceSource(): ?string
    {
        return $this->priceSource;
    }

    public function setPriceSource(?string $priceSource): self
    {
        $this->priceSource = $priceSource;
        return $this;
    }

    public function getOpenSource(): ?string
    {
        return $this->openSource;
    }

    public function setOpenSource(?string $openSource): self
    {
        $this->openSource = $openSource;
        return $this;
    }

    public function getHighSource(): ?string
    {
        return $this->highSource;
    }

    public function setHighSource(?string $highSource): self
    {
        $this->highSource = $highSource;
        return $this;
    }

    public function getLowSource(): ?string
    {
        return $this->lowSource;
    }

    public function setLowSource(?string $lowSource): self
    {
        $this->lowSource = $lowSource;
        return $this;
    }

    public function getCloseSource(): ?string
    {
        return $this->closeSource;
    }

    public function setCloseSource(?string $closeSource): self
    {
        $this->closeSource = $closeSource;
        return $this;
    }

    public function getVolumeSource(): ?string
    {
        return $this->volumeSource;
    }

    public function setVolumeSource(?string $volumeSource): self
    {
        $this->volumeSource = $volumeSource;
        return $this;
    }

    public function getMarketCapSource(): ?string
    {
        return $this->marketCapSource;
    }

    public function setMarketCapSource(?string $marketCapSource): self
    {
        $this->marketCapSource = $marketCapSource;
        return $this;
    }

    public function getPeSource(): ?string
    {
        return $this->peSource;
    }

    public function setPeSource(?string $peSource): self
    {
        $this->peSource = $peSource;
        return $this;
    }

    public function getEpsSource(): ?string
    {
        return $this->epsSource;
    }

    public function setEpsSource(?string $epsSource): self
    {
        $this->epsSource = $epsSource;
        return $this;
    }

    public function getDividendYieldSource(): ?string
    {
        return $this->dividendYieldSource;
    }

    public function setDividendYieldSource(?string $dividendYieldSource): self
    {
        $this->dividendYieldSource = $dividendYieldSource;
        return $this;
    }

    public function getRevenueTtmSource(): ?string
    {
        return $this->revenueTtmSource;
    }

    public function setRevenueTtmSource(?string $revenueTtmSource): self
    {
        $this->revenueTtmSource = $revenueTtmSource;
        return $this;
    }

    public function getNetIncomeTtmSource(): ?string
    {
        return $this->netIncomeTtmSource;
    }

    public function setNetIncomeTtmSource(?string $netIncomeTtmSource): self
    {
        $this->netIncomeTtmSource = $netIncomeTtmSource;
        return $this;
    }

    public function getGrossMarginSource(): ?string
    {
        return $this->grossMarginSource;
    }

    public function setGrossMarginSource(?string $grossMarginSource): self
    {
        $this->grossMarginSource = $grossMarginSource;
        return $this;
    }

    public function getOperatingMarginSource(): ?string
    {
        return $this->operatingMarginSource;
    }

    public function setOperatingMarginSource(?string $operatingMarginSource): self
    {
        $this->operatingMarginSource = $operatingMarginSource;
        return $this;
    }

    public function getProfitMarginSource(): ?string
    {
        return $this->profitMarginSource;
    }

    public function setProfitMarginSource(?string $profitMarginSource): self
    {
        $this->profitMarginSource = $profitMarginSource;
        return $this;
    }

    public function getDividendPerShareSource(): ?string
    {
        return $this->dividendPerShareSource;
    }

    public function setDividendPerShareSource(?string $dividendPerShareSource): self
    {
        $this->dividendPerShareSource = $dividendPerShareSource;
        return $this;
    }

    public function getPayoutRatioSource(): ?string
    {
        return $this->payoutRatioSource;
    }

    public function setPayoutRatioSource(?string $payoutRatioSource): self
    {
        $this->payoutRatioSource = $payoutRatioSource;
        return $this;
    }

    public function getRevenuePerShareSource(): ?string
    {
        return $this->revenuePerShareSource;
    }

    public function setRevenuePerShareSource(?string $revenuePerShareSource): self
    {
        $this->revenuePerShareSource = $revenuePerShareSource;
        return $this;
    }

    public function getEpsDilutedSource(): ?string
    {
        return $this->epsDilutedSource;
    }

    public function setEpsDilutedSource(?string $epsDilutedSource): self
    {
        $this->epsDilutedSource = $epsDilutedSource;
        return $this;
    }

    public function getSharesOutstandingSource(): ?string
    {
        return $this->sharesOutstandingSource;
    }

    public function setSharesOutstandingSource(?string $sharesOutstandingSource): self
    {
        $this->sharesOutstandingSource = $sharesOutstandingSource;
        return $this;
    }

    public function getFloatSharesSource(): ?string
    {
        return $this->floatSharesSource;
    }

    public function setFloatSharesSource(?string $floatSharesSource): self
    {
        $this->floatSharesSource = $floatSharesSource;
        return $this;
    }

    public function getBetaSource(): ?string
    {
        return $this->betaSource;
    }

    public function setBetaSource(?string $betaSource): self
    {
        $this->betaSource = $betaSource;
        return $this;
    }

    public function getWeek52HighSource(): ?string
    {
        return $this->week52HighSource;
    }

    public function setWeek52HighSource(?string $week52HighSource): self
    {
        $this->week52HighSource = $week52HighSource;
        return $this;
    }

    public function getWeek52LowSource(): ?string
    {
        return $this->week52LowSource;
    }

    public function setWeek52LowSource(?string $week52LowSource): self
    {
        $this->week52LowSource = $week52LowSource;
        return $this;
    }

    public function getAvgVolumeSource(): ?string
    {
        return $this->avgVolumeSource;
    }

    public function setAvgVolumeSource(?string $avgVolumeSource): self
    {
        $this->avgVolumeSource = $avgVolumeSource;
        return $this;
    }

    public function getEnterpriseValueSource(): ?string
    {
        return $this->enterpriseValueSource;
    }

    public function setEnterpriseValueSource(?string $enterpriseValueSource): self
    {
        $this->enterpriseValueSource = $enterpriseValueSource;
        return $this;
    }

    public function getEbitdaTtmSource(): ?string
    {
        return $this->ebitdaTtmSource;
    }

    public function setEbitdaTtmSource(?string $ebitdaTtmSource): self
    {
        $this->ebitdaTtmSource = $ebitdaTtmSource;
        return $this;
    }

    public function getFreeCashFlowTtmSource(): ?string
    {
        return $this->freeCashFlowTtmSource;
    }

    public function setFreeCashFlowTtmSource(?string $freeCashFlowTtmSource): self
    {
        $this->freeCashFlowTtmSource = $freeCashFlowTtmSource;
        return $this;
    }

    public function getOperatingCashFlowTtmSource(): ?string
    {
        return $this->operatingCashFlowTtmSource;
    }

    public function setOperatingCashFlowTtmSource(?string $operatingCashFlowTtmSource): self
    {
        $this->operatingCashFlowTtmSource = $operatingCashFlowTtmSource;
        return $this;
    }

    public function getGrossProfitTtmSource(): ?string
    {
        return $this->grossProfitTtmSource;
    }

    public function setGrossProfitTtmSource(?string $grossProfitTtmSource): self
    {
        $this->grossProfitTtmSource = $grossProfitTtmSource;
        return $this;
    }

    public function getTotalDebtSource(): ?string
    {
        return $this->totalDebtSource;
    }

    public function setTotalDebtSource(?string $totalDebtSource): self
    {
        $this->totalDebtSource = $totalDebtSource;
        return $this;
    }

    public function getTotalCashSource(): ?string
    {
        return $this->totalCashSource;
    }

    public function setTotalCashSource(?string $totalCashSource): self
    {
        $this->totalCashSource = $totalCashSource;
        return $this;
    }

    public function getDebtToEquitySource(): ?string
    {
        return $this->debtToEquitySource;
    }

    public function setDebtToEquitySource(?string $debtToEquitySource): self
    {
        $this->debtToEquitySource = $debtToEquitySource;
        return $this;
    }

    public function getCurrentRatioSource(): ?string
    {
        return $this->currentRatioSource;
    }

    public function setCurrentRatioSource(?string $currentRatioSource): self
    {
        $this->currentRatioSource = $currentRatioSource;
        return $this;
    }

    public function getQuickRatioSource(): ?string
    {
        return $this->quickRatioSource;
    }

    public function setQuickRatioSource(?string $quickRatioSource): self
    {
        $this->quickRatioSource = $quickRatioSource;
        return $this;
    }

    public function getPriceToBookSource(): ?string
    {
        return $this->priceToBookSource;
    }

    public function setPriceToBookSource(?string $priceToBookSource): self
    {
        $this->priceToBookSource = $priceToBookSource;
        return $this;
    }

    public function getPriceToSalesSource(): ?string
    {
        return $this->priceToSalesSource;
    }

    public function setPriceToSalesSource(?string $priceToSalesSource): self
    {
        $this->priceToSalesSource = $priceToSalesSource;
        return $this;
    }

    public function getPegRatioSource(): ?string
    {
        return $this->pegRatioSource;
    }

    public function setPegRatioSource(?string $pegRatioSource): self
    {
        $this->pegRatioSource = $pegRatioSource;
        return $this;
    }

    public function getEvToEbitdaSource(): ?string
    {
        return $this->evToEbitdaSource;
    }

    public function setEvToEbitdaSource(?string $evToEbitdaSource): self
    {
        $this->evToEbitdaSource = $evToEbitdaSource;
        return $this;
    }

    public function getEvToRevenueSource(): ?string
    {
        return $this->evToRevenueSource;
    }

    public function setEvToRevenueSource(?string $evToRevenueSource): self
    {
        $this->evToRevenueSource = $evToRevenueSource;
        return $this;
    }

    public function getBookValuePerShareSource(): ?string
    {
        return $this->bookValuePerShareSource;
    }

    public function setBookValuePerShareSource(?string $bookValuePerShareSource): self
    {
        $this->bookValuePerShareSource = $bookValuePerShareSource;
        return $this;
    }

    public function getRoaSource(): ?string
    {
        return $this->roaSource;
    }

    public function setRoaSource(?string $roaSource): self
    {
        $this->roaSource = $roaSource;
        return $this;
    }

    public function getRoeSource(): ?string
    {
        return $this->roeSource;
    }

    public function setRoeSource(?string $roeSource): self
    {
        $this->roeSource = $roeSource;
        return $this;
    }

    public function getRoiSource(): ?string
    {
        return $this->roiSource;
    }

    public function setRoiSource(?string $roiSource): self
    {
        $this->roiSource = $roiSource;
        return $this;
    }
}
