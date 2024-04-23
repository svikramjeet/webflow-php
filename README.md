# connect with webflow via API

[![Latest Version on Packagist](https://img.shields.io/packagist/v/svikramjeet/webflow.svg?style=flat-square)](https://packagist.org/packages/svikramjeet/webflow)
[![Total Downloads](https://img.shields.io/packagist/dt/svikramjeet/webflow.svg?style=flat-square)](https://packagist.org/packages/svikramjeet/webflow)

Webflwo API wrapper for PHP and laravel

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/webflow.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/webflow)


## Installation

You can install the package via composer:

```bash
composer require svikramjeet/webflow
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="webflow-config"
```

This is the contents of the published config file:

```php
return [
    'token' => env('WEBFLOW_TOKEN'),
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="webflow-views"
```

## Usage

```php
$webflow = new Svikramjeet\Webflow();
echo $webflow->info();
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Vikramjeet Singh](https://github.com/svikramjeet)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
