<?php
namespace ScriptFUSION\Porter\Provider\EuropeanCentralBank\Records;

use ScriptFUSION\Porter\Collection\CountableProviderRecords;
use ScriptFUSION\Porter\Provider\Resource\ProviderResource;

class CurrencyRecords extends CountableProviderRecords
{
    private $date;

    public function __construct(
        \Iterator $providerRecords,
        \DateTimeImmutable $date,
        $count,
        ProviderResource $resource
    ) {
        parent::__construct($providerRecords, $count, $resource);

        $this->date = $date;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Converts the records to an associative array.
     *
     * @return float[] Currency code as key and exchange rate as value.
     */
    public function toAssociativeArray()
    {
        return array_column(iterator_to_array($this), 'rate', 'currency');
    }
}
