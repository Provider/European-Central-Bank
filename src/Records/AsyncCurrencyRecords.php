<?php
declare(strict_types=1);

namespace ScriptFUSION\Porter\Provider\EuropeanCentralBank\Records;

use Amp\Iterator;
use Amp\Promise;
use ScriptFUSION\Porter\Collection\AsyncProviderRecords;
use ScriptFUSION\Porter\Provider\Resource\AsyncResource;
use function Amp\call;

class AsyncCurrencyRecords extends AsyncProviderRecords
{
    private $date;

    private $count;

    /**
     * @param Iterator $providerRecords
     * @param Promise<\DateTimeImmutable> $date
     * @param Promise<int> $count
     * @param AsyncResource $resource
     */
    public function __construct(
        Iterator $providerRecords,
        Promise $date,
        Promise $count,
        AsyncResource $resource
    ) {
        parent::__construct($providerRecords, $resource);

        $this->date = $date;
        $this->count = $count;
    }

    /**
     * @return Promise<\DateTimeImmutable>
     */
    public function getDate(): Promise
    {
        return $this->date;
    }

    /**
     * @return Promise<int>
     */
    public function getCount(): Promise
    {
        return $this->count;
    }

    /**
     * Converts the records to an associative array.
     *
     * @return Promise<float[]> Currency code as key and exchange rate as value.
     */
    public function toAssociativeArray(): Promise
    {
        return call(function (): \Generator {
            return array_column(yield Iterator\toArray($this), 'rate', 'currency');
        });
    }
}
