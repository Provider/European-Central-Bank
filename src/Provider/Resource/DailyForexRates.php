<?php
declare(strict_types=1);

namespace ScriptFUSION\Porter\Provider\EuropeanCentralBank\Provider\Resource;

use ScriptFUSION\Porter\Connector\ImportConnector;
use ScriptFUSION\Porter\Net\Http\HttpDataSource;
use ScriptFUSION\Porter\Provider\EuropeanCentralBank\Provider\EuropeanCentralBankProvider;
use ScriptFUSION\Porter\Provider\EuropeanCentralBank\Records\CurrencyRecords;
use ScriptFUSION\Porter\Provider\Resource\ProviderResource;

class DailyForexRates implements ProviderResource
{
    private const URL = 'http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';

    public function getProviderClassName(): string
    {
        return EuropeanCentralBankProvider::class;
    }

    public function fetch(ImportConnector $connector): \Iterator
    {
        $xmlString = $connector->fetch(new HttpDataSource(self::URL));
        $xml = simplexml_load_string((string)$xmlString);

        $ratesContainer = $xml->Cube->Cube;
        $date = (string)$ratesContainer['time'];

        $rates = $ratesContainer->Cube;
        $currencies = static function () use ($rates): \Generator {
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
