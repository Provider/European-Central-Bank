<?php
namespace ScriptFUSION\Porter\Provider\EuropeanCentralBank;

use ScriptFUSION\Porter\Collection\CountableProviderRecords;
use ScriptFUSION\Porter\Provider\ProviderDataFetcher;

class CurrencyRecords extends CountableProviderRecords
{
    private $date;

    public function __construct(
        \Iterator $providerRecords,
        \DateTimeImmutable $date,
        $count,
        ProviderDataFetcher $dataFetcher
    ) {
        parent::__construct($providerRecords, $count, $dataFetcher);

        $this->date = $date;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getDate()
    {
        return $this->date;
    }
}
