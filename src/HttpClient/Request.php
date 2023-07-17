<?php

namespace Vendloop\HttpClient;

use \Vendloop\Helpers\Router;

class Request
{
    public $method;
    public $endpoint;
    public $body = '';
    public $headers = [];
    protected $response;
    protected $config;

    public function __construct($config = null)
    {
        $this->response = new Response();
        $this->response->setRequestObject($this);
        $this->config = $config;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function flattenedHeaders()
    {
        $headers = [];
        foreach ($this->headers as $key => $value) {
            $headers[] = $key . ": " . $value;
        }
        return $headers;
    }

    public function send()
    {
		if ($this->config->use_guzzle) {
			$this->withGuzzle();
		} else {
			$this->withCurl();
		}
        return $this->response;
    }

    public function withGuzzle()
    {
        if (class_exists('\\GuzzleHttp\\Client') && class_exists('\\GuzzleHttp\\Psr7\\Request')) {
            $request = new \GuzzleHttp\Psr7\Request(
                strtoupper($this->method),
                $this->endpoint,
                $this->headers,
                $this->body
            );
            $client = new \GuzzleHttp\Client();
            try {
                $psr7response = $client->send($request);
                $this->response->body = $psr7response->getBody()->getContents();
                $this->response->okay = true;
            } catch (\Exception $e) {
                if (($e instanceof \GuzzleHttp\Exception\BadResponseException
                    || $e instanceof \GuzzleHttp\Exception\ClientException
                    || $e instanceof \GuzzleHttp\Exception\ConnectException
                    || $e instanceof \GuzzleHttp\Exception\RequestException
                    || $e instanceof \GuzzleHttp\Exception\ServerException)
                ) {
                    if ($e->hasResponse()) {
                        $this->response->body = $e->getResponse()->getBody()->getContents();
                    }
                    $this->response->okay = true;
                }
                $this->response->messages[] = $e->getMessage();
            }
        } else {
            $this->response->messages[] = 'GuzzleHttp not installed';
		}
    }

    public function withCurl()
    {
        //open connection
        $ch = \curl_init();
        \curl_setopt($ch, \CURLOPT_URL, $this->endpoint);
        ($this->method === Router::POST_METHOD) && \curl_setopt($ch, \CURLOPT_POST, true);
        ($this->method === Router::PUT_METHOD) && \curl_setopt($ch, \CURLOPT_CUSTOMREQUEST, 'PUT');

        if ($this->method !== Router::GET_METHOD) {
            \curl_setopt($ch, \CURLOPT_POSTFIELDS, $this->body);
        }
        \curl_setopt($ch, \CURLOPT_HTTPHEADER, $this->flattenedHeaders());
        \curl_setopt($ch, \CURLOPT_RETURNTRANSFER, 1);
        \curl_setopt($ch, \CURLOPT_SSLVERSION, 6);

        $this->response->body = \curl_exec($ch);

        if (\curl_errno($ch)) {
            $cerr = \curl_error($ch);
            $this->response->messages[] = 'Curl failed with response: \'' . $cerr . '\'.';
        } else {
            $this->response->okay = true;
        }

        \curl_close($ch);
    }
}
