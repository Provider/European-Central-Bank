<?php
declare(strict_types=1);

namespace ScriptFUSION\Porter\Provider\EuropeanCentralBank\Provider\Resource;

use Amp\Deferred;
use Amp\Iterator;
use Amp\Producer;
use ScriptFUSION\Porter\Connector\ImportConnector;
use ScriptFUSION\Porter\Net\Http\AsyncHttpDataSource;
use ScriptFUSION\Porter\Net\Http\HttpDataSource;
use ScriptFUSION\Porter\Net\Http\HttpResponse;
use ScriptFUSION\Porter\Provider\EuropeanCentralBank\Provider\EuropeanCentralBankProvider;
use ScriptFUSION\Porter\Provider\EuropeanCentralBank\Records\AsyncCurrencyRecords;
use ScriptFUSION\Porter\Provider\EuropeanCentralBank\Records\CurrencyRecords;
use ScriptFUSION\Porter\Provider\Resource\AsyncResource;
use ScriptFUSION\Porter\Provider\Resource\ProviderResource;

class DailyForexRates implements ProviderResource, AsyncResource
{
    private const URL = 'http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';

    public function getProviderClassName(): string
    {
        return EuropeanCentralBankProvider::class;
    }

    public function fetch(ImportConnector $connector): \Iterator
    {
        $response = $connector->fetch(new HttpDataSource(self::URL));
        [$date, $rates, $currencies] = self::parseResponse($response);

        return new CurrencyRecords($currencies(), $date, count($rates), $this);
    }

    public function fetchAsync(ImportConnector $connector): Iterator
    {
        $dateDeferred = new Deferred;
        $totalRatesDeferred = new Deferred;

        return new AsyncCurrencyRecords(new Producer(
            static function (\Closure $emit) use ($connector, $dateDeferred, $totalRatesDeferred): \Generator {
                $response = yield $connector->fetchAsync(new AsyncHttpDataSource(self::URL));
                [$date, $rates, $currencies] = self::parseResponse($response);

                $dateDeferred->resolve($date);
                $totalRatesDeferred->resolve(count($rates));

                foreach ($currencies() as $currency) {
                    yield $emit($currency);
                }
            }
        ), $dateDeferred->promise(), $totalRatesDeferred->promise(), $this);
    }

    private static function parseResponse(HttpResponse $response): array
    {
        $xml = simplexml_load_string((string)$response);

        $ratesContainer = $xml->Cube->Cube;
        $date = new \DateTimeImmutable((string)$ratesContainer['time']);

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

        return [$date, $rates, $currencies];
    }
}
