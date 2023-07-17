<?php

namespace Vendloop\Routes;

use \Vendloop\Helpers\Router;

class Sales
{

    public static function root()
    {
        return '/sales';
    }

    public static function fetch()
    {
        return [
            Router::METHOD_KEY => Router::GET_METHOD,
            Router::ENDPOINT_KEY => self::root() . '/{id}',
            Router::ARGS_KEY => ['id'],
            Router::REQUIRED_KEY => [Router::ARGS_KEY => ['id']],
        ];
    }

    public static function list()
    {
        return [
            Router::METHOD_KEY => Router::GET_METHOD,
            Router::ENDPOINT_KEY => self::root(),
            Router::PARAMS_KEY => [
                'id',
                'customer_id',
                'product_id',
                'variant_id',
                'employee_id',
                'start_date',
                'end_date',
            ],
        ];
    }

    public static function delete()
    {
        return [
            Router::METHOD_KEY => Router::POST_METHOD,
            Router::ENDPOINT_KEY => self::root() . '/delete',
            Router::PARAMS_KEY => [
                'id',
            ],
            Router::REQUIRED_KEY => [Router::PARAMS_KEY => ['id']],
        ];
    }
}
