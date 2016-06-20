<?php
namespace ScriptFUSION\Porter\Provider\EuropeanCentralBank;

use ScriptFUSION\Porter\Net\Http\HttpConnector;
use ScriptFUSION\Porter\Provider\Provider;

final class EcbProvider extends Provider
{
    public function __construct(HttpConnector $connector = null)
    {
        parent::__construct($connector ?: new HttpConnector);
    }
}
