# :package_description

[![PHP Version Require](https://poser.pugx.org/:vendor_slug/:package_slug/require/php)](https://packagist.org/packages/:vendor_slug/:package_slug)
[![Latest Stable Version](https://poser.pugx.org/:vendor_slug/:package_slug/v/stable)](https://packagist.org/packages/:vendor_slug/:package_slug)
[![phpunit](https://github.com/:vendor_slug/:package_slug/actions/workflows/phpunit.yml/badge.svg)](https://github.com/:vendor_slug/:package_slug/actions)
[![psalm](https://github.com/:vendor_slug/:package_slug/actions/workflows/psalm.yml/badge.svg)](https://github.com/:vendor_slug/:package_slug/actions)
[![Codecov](https://codecov.io/gh/:vendor_slug/:package_slug/branch/master/graph/badge.svg)](https://codecov.io/gh/:vendor_slug/:package_slug/)
[![Total Downloads](https://poser.pugx.org/:vendor_slug/:package_slug/downloads)](https://packagist.org/:vendor_slug/:package_slug/phpunit)

<!--delete-->
---
This repo can be used to scaffold a Spiral Framework package. Follow these steps to get started:

1. Press the "Use template" button at the top of this repo to create a new repo with the contents of this skeleton.
2. Run `php ./configure.php` to run a script that will replace all placeholders throughout all the files.
---
<!--/delete-->
This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.


## Requirements

Make sure that your server is configured with following PHP version and extensions:

- PHP 8.1+
- Spiral framework 3.0+

## Installation

You can install the package via composer:

```bash
composer require :vendor_slug/:package_slug
```

After package install you need to register bootloader from the package.

```php
protected const LOAD = [
    // ...
    \VendorName\Skeleton\Bootloader\SkeletonBootloader::class,
];
```

> Note: if you are using [`spiral-packages/discoverer`](https://github.com/spiral-packages/discoverer),
> you don't need to register bootloader by yourself.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [:author_name](https://github.com/:author_username)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
