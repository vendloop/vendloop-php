<?php

namespace Vendloop\Helpers;

use \Vendloop\HttpClient\RequestBuilder;

class Caller
{
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function callEndpoint($interface, $payload = [], $sentargs = [])
    {
        $builder = new RequestBuilder($this->config, $interface, $payload, $sentargs);
        return $builder->build()->send()->wrapUp();
    }
}
