<?php
declare(strict_types=1);

namespace ScriptFUSIONTest\Porter\Provider\EuropeanCentralBank;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use ScriptFUSION\Porter\Import\Import;
use ScriptFUSION\Porter\Porter;
use ScriptFUSION\Porter\Provider\EuropeanCentralBank\Provider\EuropeanCentralBankProvider;
use ScriptFUSION\Porter\Provider\EuropeanCentralBank\Provider\Resource\DailyForexRates;
use ScriptFUSION\Porter\Provider\EuropeanCentralBank\Records\CurrencyRecords;

final class DailyForexRatesTest extends TestCase
{
    private Porter $porter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->porter = new Porter(
            \Mockery::mock(ContainerInterface::class)
                ->shouldReceive('has')
                    ->with(EuropeanCentralBankProvider::class)
                    ->andReturn(true)
                ->shouldReceive('get')
                    ->with(EuropeanCentralBankProvider::class)
                    ->andReturn(new EuropeanCentralBankProvider)
                ->getMock()
        );
    }

    public function testSync(): void
    {
        $fxRates = $this->porter->import(new Import(new DailyForexRates));

        /** @var CurrencyRecords $currencyRecords */
        $currencyRecords = $fxRates->findFirstCollection();

        // There must be at least 25 exchange rates.
        self::assertGreaterThan(25, count($currencyRecords));

        $rates = $currencyRecords->toAssociativeArray();

        // Ensure major world currencies are available.
        foreach (['USD', 'GBP', 'JPY'] as $currency) {
            self::assertArrayHasKey($currency, $rates);
        }

        // Each rate must be a non-zero, positive float.
        foreach ($rates as $rate) {
            self::assertIsFloat($rate);
            self::assertGreaterThan(0, $rate);
        }
    }
}
