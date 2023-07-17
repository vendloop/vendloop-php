<?php

namespace Vendloop;

use \Vendloop\Helpers\Router;
use \Vendloop\Exception\VendloopException;

class VendloopClient
{
    public $base_url = 'https://api.vendloop.com/';
    public $version = "1.0.0";
    public $config;
    private $defaultOpts;

    public function __construct($config = [])
    {
		
        if (\is_string($config)) {
            $config = ['api_key' => $config];
        } elseif (!\is_array($config)) {
            throw new VendloopException('$config must be a string or an array');
        }
		
        $config = \array_merge($this->getDefaultConfig(), $config);
        $this->validateConfig($config);
		
		$config['base_url'] = trim($config['base_url'], '/');
		$config['version'] = $this->version;
		
        $this->config = (object) $config;
		
    }

    public function __call($method, $args)
    {
        if (count($args) === 1) {
            $args = [[], [Router::ID_KEY => $args[0]]];
            return $this->{$method}->__call('fetch', $args);
        } elseif ((count($args) === 1 && is_array($args[0]))||(count($args) === 0)) {
            return $this->{$method}->__call('list', $args);
        }
        throw new VendloopException(
            'Route "' . $method . '" does not exist.'
        );
		
    }

    public function __get($name)
    {
        return new Router($name, $this->config);
    }
	
	/**
     * TODO: replace this with a private constant when we drop support for PHP < 5.
     *
     * @return array<string, mixed>
     */
    private function getDefaultConfig()
    {
        return [
            'api_key' => null,
            'use_guzzle' => false,
            'base_url' => $this->base_url,
        ];
    }

    /**
     * @param array<string, mixed> $config
     *
     * @throws VendloopException
     */
    private function validateConfig($config)
    {
        // api_key
        if (!array_key_exists('api_key', $config)) {
            $msg = 'No API key provided.  (HINT: set your API key using '
              . '"Vendloop::setApiKey(<API-KEY>)".  You can generate API keys from '
              . 'the Vendloop web interface.  See https://vendloop.com/docs/api for '
              . 'details, or email support@vendloop.com if you have any questions.';

            throw new VendloopException($msg);
        }
		
        if (null == $config['api_key'] && !\is_string($config['api_key'])) {
            throw new VendloopException('api_key cannot be empty and must be a string');
        }

        if (null !== $config['api_key'] && ('' === $config['api_key'])) {
            throw new VendloopException('api_key cannot be an empty string');
        }

        if (null !== $config['api_key'] && (\preg_match('/\s/', $config['api_key']))) {
            throw new VendloopException('api_key cannot contain whitespace');
        }

        // base_url
        if (!\is_string($config['base_url'])) {
            throw new VendloopException('base_url must be a string');
        }
		
        // use_guzzle
        if (null !== $config['use_guzzle'] && !\is_bool($config['use_guzzle'])) {
            throw new VendloopException('use_guzzle must be null or a boolean');
        }
		
        // check absence of extra keys
        $extraConfigKeys = \array_diff(\array_keys($config), \array_keys($this->getDefaultConfig()));
        if (!empty($extraConfigKeys)) {
            // Wrap in single quote to more easily catch trailing spaces errors
            $invalidKeys = "'" . \implode("', '", $extraConfigKeys) . "'";

            throw new VendloopException('Found unknown key(s) in configuration array: ' . $invalidKeys);
        }
    }
}
