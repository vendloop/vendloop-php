<?php

namespace Vendloop\HttpClient;

use \Vendloop\Helpers\Router;
use \Vendloop\VendloopClient;

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
        $this->response->forApi = !is_null($config);
        if ($this->response->forApi) {
            $this->headers['Content-Type'] = 'application/json';
        }
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
		if ($this->config->use_guzzlehttp) {
			$this->withGuzzle();
		} else {
			$this->withCurl();
		}
        if (!$this->response->okay) {
            $this->withFileGetContents();
        }
        return $this->response;
    }

    public function withGuzzle()
    {
        if (isset($this->config) && !$this->config->use_guzzlehttp) {
            $this->response->okay = false;
            return;
        }
        if (class_exists('\\GuzzleHttp\\Exception\\BadResponseException')
            && class_exists('\\GuzzleHttp\\Exception\\ClientException')
            && class_exists('\\GuzzleHttp\\Exception\\ConnectException')
            && class_exists('\\GuzzleHttp\\Exception\\RequestException')
            && class_exists('\\GuzzleHttp\\Exception\\ServerException')
            && class_exists('\\GuzzleHttp\\Client')
            && class_exists('\\GuzzleHttp\\Psr7\\Request')
        ) {
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
					$message = 'GuzzleHttp failed';
                    $this->response->okay = true;
                    if ($e->hasResponse()) {
						$message = $e->getMessage();
                        $this->response->body = $e->getResponse()->getBody()->getContents();
                    } else {
						$this->response->body = $message;
					}
                }
                $this->response->messages[] = $message;
            }
        } else {
            $this->response->messages[] = 'GuzzleHttp is not installed';
		}
    }

    public function withFileGetContents()
    {
        if (!VendloopClient::$fallback_to_file_get_contents) {
            return;
        }
        $context = stream_context_create(
            [
                'http' => [
                  'method'=>$this->method,
                  'header'=>$this->flattenedHeaders(),
                  'content'=>$this->body,
                  'ignore_errors' => true
                ]
            ]
        );
        $this->response->body = file_get_contents($this->endpoint, false, $context);
        if ($this->response->body === false) {
            $this->response->messages[] = 'file_get_contents failed with response: \'' . error_get_last() . '\'.';
        } else {
            $this->response->okay = true;
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
