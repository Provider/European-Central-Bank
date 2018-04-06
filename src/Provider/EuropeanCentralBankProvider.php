<?php
namespace ScriptFUSION\Porter\Provider\EuropeanCentralBank\Provider;

use ScriptFUSION\Porter\Net\Http\HttpConnector;
use ScriptFUSION\Porter\Provider\Provider;

final class EuropeanCentralBankProvider implements Provider
{
    private $connector;

    public function __construct(HttpConnector $connector = null)
    {
        $this->connector = $connector ?: new HttpConnector;
    }

    public function getConnector()
    {
        return $this->connector;
    }
}
