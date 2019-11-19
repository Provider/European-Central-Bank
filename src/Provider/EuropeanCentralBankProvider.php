<?php
declare(strict_types=1);

namespace ScriptFUSION\Porter\Provider\EuropeanCentralBank\Provider;

use ScriptFUSION\Porter\Connector\AsyncConnector;
use ScriptFUSION\Porter\Connector\Connector;
use ScriptFUSION\Porter\Net\Http\AsyncHttpConnector;
use ScriptFUSION\Porter\Net\Http\HttpConnector;
use ScriptFUSION\Porter\Provider\AsyncProvider;
use ScriptFUSION\Porter\Provider\Provider;

final class EuropeanCentralBankProvider implements Provider, AsyncProvider
{
    private $connector;

    private $asyncConnector;

    public function __construct(Connector $connector = null, AsyncConnector $asyncConnector = null)
    {
        $this->connector = $connector ?: new HttpConnector;
        $this->asyncConnector = $asyncConnector ?: new AsyncHttpConnector;
    }

    public function getConnector(): Connector
    {
        return $this->connector;
    }

    public function getAsyncConnector(): AsyncConnector
    {
        return $this->asyncConnector;
    }
}
