<?php

namespace Vendloop\HttpClient;

use \Vendloop\Helpers\Router;

class RequestBuilder
{
    protected $config;
    protected $interface;
    protected $request;

    public $payload = [];
    public $sentargs = [];

    public function __construct($config, $interface, array $payload = [], array $sentargs = [])
    {
        $this->request = new Request($config);
        $this->config = $config;
        $this->interface = $interface;
        $this->payload = $payload;
        $this->sentargs = $sentargs;
    }

    public function build()
    {
        $this->request->headers["Authorization"] = "Bearer " . $this->config->api_key;
        $this->request->headers["User-Agent"] = "Vendloop-php/v" . $this->config->version;
		$this->request->headers['Content-Type'] = 'application/json';
		$this->request->headers['Expect'] = ' ';
        $this->request->endpoint = $this->config->base_url . $this->interface[Router::ENDPOINT_KEY];
        $this->request->method = $this->interface[Router::METHOD_KEY];
        $this->moveArgsToSentargs();
        $this->putArgsIntoEndpoint($this->request->endpoint);
        $this->packagePayload();
        return $this->request;
    }

    public function packagePayload()
    {
        if (is_array($this->payload) && count($this->payload)) {
            if ($this->request->method === Router::GET_METHOD) {
                $this->request->endpoint = $this->request->endpoint . '?' . http_build_query($this->payload);
            } else {
                $this->request->body = json_encode($this->payload);
            }
        }
    }

    public function putArgsIntoEndpoint(&$endpoint)
    {
        foreach ($this->sentargs as $key => $value) {
            $endpoint = str_replace('{' . $key . '}', $value, $endpoint);
        }
    }

    public function moveArgsToSentargs()
    {
        if (!array_key_exists(Router::ARGS_KEY, $this->interface)) {
            return;
        }
        $args = $this->interface[Router::ARGS_KEY];
        foreach ($this->payload as $key => $value) {
            if (in_array($key, $args)) {
                $this->sentargs[$key] = $value;
                unset($this->payload[$key]);
            }
        }
    }
}
