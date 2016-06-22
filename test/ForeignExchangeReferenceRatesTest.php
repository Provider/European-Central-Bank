<?php
namespace ScriptFUSIONTest\Porter\Provider\EuropeanCentralBank;

use ScriptFUSION\Porter\Porter;
use ScriptFUSION\Porter\Provider\EuropeanCentralBank\CurrencyRecords;
use ScriptFUSION\Porter\Provider\EuropeanCentralBank\EuropeanCentralBankProvider;
use ScriptFUSION\Porter\Provider\EuropeanCentralBank\ForeignExchangeReferenceRates;
use ScriptFUSION\Porter\Specification\ImportSpecification;

final class ForeignExchangeReferenceRatesTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $porter = (new Porter)->addProvider(new EuropeanCentralBankProvider);
        $fxRates = $porter->import(new ImportSpecification(new ForeignExchangeReferenceRates));

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
