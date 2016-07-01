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
        $xmlString = $connector->fetch(self::URL);
        $xml = simplexml_load_string($xmlString);

        $ratesContainer = $xml->Cube->Cube;
        $date = (string) $ratesContainer['time'];
        $rates = $ratesContainer->Cube;

        $currencies = function () use ($rates) {
            /** @var \SimpleXMLElement[] $rates */
            foreach ($rates as $rate) {
                yield [
                    'currency' => (string)$rate['currency'],
                    'rate' => (float)$rate['rate'],
                ];
            }
        };

        return new CurrencyRecords($currencies(), new \DateTimeImmutable($date), count($rates), $this);
    }
}
