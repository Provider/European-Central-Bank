<?php
declare(strict_types=1);

namespace ScriptFUSIONTest\Porter\Provider\EuropeanCentralBank;

use Amp\Loop;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use ScriptFUSION\Porter\Porter;
use ScriptFUSION\Porter\Provider\EuropeanCentralBank\Provider\EuropeanCentralBankProvider;
use ScriptFUSION\Porter\Provider\EuropeanCentralBank\Provider\Resource\DailyForexRates;
use ScriptFUSION\Porter\Provider\EuropeanCentralBank\Records\AsyncCurrencyRecords;
use ScriptFUSION\Porter\Provider\EuropeanCentralBank\Records\CurrencyRecords;
use ScriptFUSION\Porter\Specification\AsyncImportSpecification;
use ScriptFUSION\Porter\Specification\ImportSpecification;

final class DailyForexRatesTest extends TestCase
{
    /** @var Porter */
    private $porter;

    protected function setUp()
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
        $fxRates = $this->porter->import(new ImportSpecification(new DailyForexRates));

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
            self::assertInternalType('float', $rate);
            self::assertGreaterThan(0, $rate);
        }
    }

    public function testAsync(): void
    {
        Loop::run(function (): \Generator {
            $fxRates = $this->porter->importAsync(new AsyncImportSpecification(new DailyForexRates));

            /** @var AsyncCurrencyRecords $currencyRecords */
            $currencyRecords = $fxRates->findFirstCollection();

            self::assertInstanceOf(\DateTimeImmutable::class, yield $currencyRecords->getDate());

            // There must be at least 25 exchange rates.
            self::assertGreaterThan(25, yield $currencyRecords->getCount());

            $rates = yield $currencyRecords->toAssociativeArray();

            // Ensure major world currencies are available.
            foreach (['USD', 'GBP', 'JPY'] as $currency) {
                self::assertArrayHasKey($currency, $rates);
            }

            // Each rate must be a non-zero, positive float.
            foreach ($rates as $rate) {
                self::assertInternalType('float', $rate);
                self::assertGreaterThan(0, $rate);
            }
        });
    }
}
