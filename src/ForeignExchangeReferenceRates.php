<?php
namespace ScriptFUSION\Porter\Provider\EuropeanCentralBank;

use ScriptFUSION\Porter\Connector\Connector;
use ScriptFUSION\Porter\Provider\ProviderDataFetcher;
use Symfony\Component\DomCrawler\Crawler;

class ForeignExchangeReferenceRates implements ProviderDataFetcher
{
    const URL = 'http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';

    public function getProviderName()
    {
        return EuropeanCentralBankProvider::class;
    }

    public function fetch(Connector $connector)
    {
        $xml = $connector->fetch(self::URL);
        $crawler = new Crawler($xml);

        $ratesContainer = $crawler->filterXPath('./*/default:Cube/default:Cube[1]');
        $date = $ratesContainer->attr('time');

        $rates = $ratesContainer->filterXPath('./*/default:Cube');
        $currencies = function () use ($rates) {
            /** @var \DOMElement[] $rates */
            foreach ($rates as $rate) {
                yield [
                    'currency' => $rate->getAttribute('currency'),
                    'rate' => (float)$rate->getAttribute('rate'),
                ];
            }
        };

        return new CurrencyRecords($currencies(), new \DateTimeImmutable($date), count($rates), $this);
    }
}
