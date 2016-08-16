<?php
namespace ScriptFUSION\Porter\Provider\EuropeanCentralBank\Provider;

use ScriptFUSION\Porter\Net\Http\HttpConnector;
use ScriptFUSION\Porter\Provider\AbstractProvider;

final class EuropeanCentralBankProvider extends AbstractProvider
{
    public function __construct(HttpConnector $connector = null)
    {
        parent::__construct($connector ?: new HttpConnector);
    }
}
