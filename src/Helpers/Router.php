<?php

namespace Vendloop\Helpers;

use \Closure;
use \Vendloop\Exception\VendloopException;

class Router
{
    private $route;
    private $route_class;
    private $methods;

    const ID_KEY = 'id';
    const METHOD_KEY = 'method';
    const ENDPOINT_KEY = 'endpoint';
    const PARAMS_KEY = 'params';
    const ARGS_KEY = 'args';
    const REQUIRED_KEY = 'required';
    const POST_METHOD = 'post';
    const PUT_METHOD = 'put';
    const GET_METHOD = 'get';
    const DELETE_METHOD = 'delete';

    public function __call($method, $args)
    {
		rsort($args);
        if (isset($args[0]) && !is_array( $args[0])) {
            $args = [[Router::ID_KEY => $args[0]]];
        }
        if (array_key_exists($method, $this->methods) && is_callable($this->methods[$method])) {
            return call_user_func_array($this->methods[$method], $args);
        } else {
            throw new VendloopException('Function "' . $method . '" does not exist for "' . $this->route . '".');
        }
    }

    public function __construct($route, $config)
    {

        $this->route = strtolower($route);
        $this->route_class = 'Vendloop\\Routes\\' . ucwords($this->route);

        $methods = get_class_methods($this->route_class);
        if (empty($methods)) {
            throw new VendloopException('Route "' . $this->route . '" does not exist.');
        }
        // add methods to this object per method, except root
        foreach ($methods as $method) {
            if ($method === 'root') {
                continue;
            }
            $mtdFunc = function (array $params = [], array $sentargs = []) use ($method, $config)
			{
                $interface = call_user_func($this->route_class . '::' . $method);
                // TODO: validate params and sentargs against definitions
                $caller = new Caller($config);
                return $caller->callEndpoint($interface, $params, $sentargs);
            };
            $this->methods[$method] = \Closure::bind($mtdFunc, $this, self::class);
        }
    }

}
