<?php

namespace App\Controller;

use App\Entity\Stock;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class StockController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route('/api/stocks', name: 'api_stocks_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $stocks = $this->entityManager->getRepository(Stock::class)->findBy([], ['id' => 'ASC']);

        $data = array_map([$this, 'serializeStock'], $stocks);

        return $this->json(['stocks' => $data]);
    }

    private function serializeStock(Stock $stock): array
    {
        return [
            'id' => $stock->getId(),
            'symbol' => $stock->getSymbol(),
            'exchangeCode' => $stock->getExchangeCode(),
            'mic' => $stock->getMic(),
            'isin' => $stock->getIsin(),
            'name' => $stock->getName(),
            'type' => $stock->getType(),
            'currency' => $stock->getCurrency(),
            'country' => $stock->getCountry(),
            'sector' => $stock->getSector(),
            'industry' => $stock->getIndustry(),
            'website' => $stock->getWebsite(),
            'description' => $stock->getDescription(),
            'logoUrl' => $stock->getLogoUrl(),
            'lastPrice' => $stock->getLastPrice(),
            'open' => $stock->getOpen(),
            'high' => $stock->getHigh(),
            'low' => $stock->getLow(),
            'prevClose' => $stock->getPrevClose(),
            'change' => $stock->getChange(),
            'changePercent' => $stock->getChangePercent(),
            'avgVolume30d' => $stock->getAvgVolume30d(),
            'marketCap' => $stock->getMarketCap(),
            'sharesOutstanding' => $stock->getSharesOutstanding(),
            'floatShares' => $stock->getFloatShares(),
            'quoteTimestamp' => $stock->getQuoteTimestamp()?->format('c'),
            'lastUpdatedAt' => $stock->getLastUpdatedAt()?->format('c'),
            'beta' => $stock->getBeta(),
            'dividendYield' => $stock->getDividendYield(),
            'dividendRate' => $stock->getDividendRate(),
            'peTtm' => $stock->getPeTtm(),
            'pb' => $stock->getPb(),
            'psTtm' => $stock->getPsTtm(),
            'evEbitda' => $stock->getEvEbitda(),
            'week52High' => $stock->getWeek52High(),
            'week52Low' => $stock->getWeek52Low(),
            'active' => $stock->isActive(),
            'dataSource' => $stock->getDataSource(),
        ];
    }
}
