<?php
namespace ScriptFUSION\Porter\Provider\EuropeanCentralBank\Provider\Resource;

use ScriptFUSION\Porter\Connector\Connector;
use ScriptFUSION\Porter\Provider\EuropeanCentralBank\Provider\EuropeanCentralBankProvider;
use ScriptFUSION\Porter\Provider\EuropeanCentralBank\Records\CurrencyRecords;
use ScriptFUSION\Porter\Provider\Resource\AbstractResource;

class ForeignExchangeReferenceRates extends AbstractResource
{
    const URL = 'http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';

    public function getProviderClassName()
    {
        return EuropeanCentralBankProvider::class;
    }

    public function fetch(Connector $connector)
    {
        $xmlString = $connector->fetch(self::URL);
        $xml = simplexml_load_string($xmlString);

        $ratesContainer = $xml->Cube->Cube;
        $date = $ratesContainer['time'];

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
