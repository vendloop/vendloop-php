# Vendloop PHP SDK

The Vendloop PHP library provides convenient access to the Vendloop API from applications written in the PHP language. It includes a pre-defined set of classes for API resources that initialize themselves dynamically from API responses which makes it compatible with a wide range of versions of the Vendloop API.



## Requirements

PHP 5.6.0 and later.



## Installation

#### Composer

You can install the bindings via [Composer](http://getcomposer.org/). Run the following command:

```bash
composer require vendloop/vendloop-php
```

To use the bindings, use Composer's [autoload](https://getcomposer.org/doc/01-basic-usage.md#autoloading):

```php
require_once 'vendor/autoload.php';
```



#### Manual Installation

If you do not wish to use Composer, you can download the [latest release](https://github.com/vendloop/vendloop-php/releases). Then, to use the bindings, include the `autoload.php` file.

```php
require_once '/path/to/vendloop-php/src/autoload.php';
```



## Dependencies

The bindings require the following extensions in order to work properly:

-   [`curl`](https://secure.php.net/manual/en/book.curl.php), although you can use your own non-cURL client if you prefer
-   [`json`](https://secure.php.net/manual/en/book.json.php)
-   [`mbstring`](https://secure.php.net/manual/en/book.mbstring.php) (Multibyte String)

If you use Composer, these dependencies should be handled automatically. If you install manually, you'll want to make sure that these extensions are available.



## Getting Started

Simple usage looks like:

```php
use Vendloop\VendloopClient;

$vendloop = new \Vendloop\VendloopClient('sk_live_aa873dabc21fba4d45762bf0b18b56d79a18e37b');
try {
    $customer = $vendloop->customers->fetch([
        'id' => 12
    ]);
    echo $customer;
} catch(\Vendloop\Exception\ApiException $e){
    die($e->getMessage());
}
```



The `VendloopClient` class also accepts an array of config values

```php
$vendloop = new \Vendloop\VendloopClient([
    'api_key' => 'sk_live_aa873dabc21fba4d45f0b18b56d79a18e37762bb', // your api key
    'base_url' => 'https://api.vendloop.com', // to change the endpoint URL to a mock server
    'use_guzzle' => false // if true, use guzzle for API calls (guzzle should be installed)
]);
```
Check [SAMPLES](SAMPLES.md) for more sample API usage



## Documentation

See the [ API docs](https://vendloop.com/docs/api/) for detailed endpoint documentation.



## SSL / TLS compatibility issues

Confirm that your server can conclude a TLSv1.2 connection to Vendloop's servers. Most up-to-date software have this capability. Contact your service provider for guidance if you have any SSL errors. Don't disable SSL peer verification!

The recommended course of action is to [upgrade your cURL and OpenSSL packages](#) so that TLS 1.2 is used by default.



## Support

New features and bug fixes are released on the latest major version of the Vendloop PHP library. If you are on an older major version, we recommend that you upgrade to the latest in order to use the new features and bug fixes including those for security vulnerabilities. Older major versions of the package will continue to be available for use, but will not be receiving any updates.



## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.



## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) and [CONDUCT](.github/CONDUCT.md) for details. Check our [TODO](.github/TODO.md) for features already intended.



## Security

If you discover any security related issues, please email <geoorg30@gmail.com> instead of using the issue tracker.



## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.