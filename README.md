# parallex-bank-api-client

[![Latest Stable Version](https://img.shields.io/github/v/release/brokeyourbike/parallex-bank-api-client-php)](https://github.com/brokeyourbike/parallex-bank-api-client-php/releases)
[![Total Downloads](https://poser.pugx.org/brokeyourbike/parallex-bank-api-client/downloads)](https://packagist.org/packages/brokeyourbike/parallex-bank-api-client)
[![Maintainability](https://api.codeclimate.com/v1/badges/b42d6d359e4f7fc486fb/maintainability)](https://codeclimate.com/github/brokeyourbike/parallex-bank-api-client-php/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/b42d6d359e4f7fc486fb/test_coverage)](https://codeclimate.com/github/brokeyourbike/parallex-bank-api-client-php/test_coverage)

Parallex Bank API Client for PHP

## Installation

```bash
composer require brokeyourbike/parallex-bank-api-client
```

## Usage

```php
use BrokeYourBike\ParallexBank\Client;
use BrokeYourBike\ParallexBank\Interfaces\ConfigInterface;

assert($config instanceof ConfigInterface);
assert($httpClient instanceof \GuzzleHttp\ClientInterface);

$apiClient = new Client($config, $httpClient);
$apiClient->postTransaction();
```

## Authors
- [Ivan Stasiuk](https://github.com/brokeyourbike) | [Twitter](https://twitter.com/brokeyourbike) | [LinkedIn](https://www.linkedin.com/in/brokeyourbike) | [stasi.uk](https://stasi.uk)

## License
[Mozilla Public License v2.0](https://github.com/brokeyourbike/parallex-bank-api-client-php/blob/main/LICENSE)