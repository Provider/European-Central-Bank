<?php
namespace ScriptFUSIONTest\Porter\Provider\EuropeanCentralBank;

use Psr\Container\ContainerInterface;
use ScriptFUSION\Porter\Porter;
use ScriptFUSION\Porter\Provider\EuropeanCentralBank\Provider\EuropeanCentralBankProvider;
use ScriptFUSION\Porter\Provider\EuropeanCentralBank\Provider\Resource\DailyForexRates;
use ScriptFUSION\Porter\Provider\EuropeanCentralBank\Records\CurrencyRecords;
use ScriptFUSION\Porter\Specification\ImportSpecification;

final class DailyForexRatesTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $porter = new Porter(
            \Mockery::mock(ContainerInterface::class)
                ->shouldReceive('has')
                    ->with(EuropeanCentralBankProvider::class)
                    ->andReturn(true)
                ->shouldReceive('get')
                    ->with(EuropeanCentralBankProvider::class)
                    ->andReturn(new EuropeanCentralBankProvider)
                ->getMock()
        );
        $fxRates = $porter->import(new ImportSpecification(new DailyForexRates));

        /** @var CurrencyRecords $currencyRecords */
        $currencyRecords = $fxRates->findFirstCollection();

        self::assertInstanceOf(\DateTimeImmutable::class, $currencyRecords->getDate());

        // There must be at least 25 exchange rates.
        self::assertGreaterThan(25, count($currencyRecords));

        $rates = $currencyRecords->toAssociativeArray();

        // Ensure major world currencies are available.
        foreach (['USD', 'GBP', 'JPY'] as $currency) {
            self::assertArrayHasKey($currency, $rates);
        }

        // Each rate must be a non-zero, positive float.
        foreach ($rates as $rate) {
            self::assertInternalType('float', $rate);
            self::assertGreaterThan(0, $rate);
        }
    }
}
