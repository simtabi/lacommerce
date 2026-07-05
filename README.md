# simtabi/lacommerce

[![Latest version on Packagist](https://img.shields.io/packagist/v/simtabi/lacommerce.svg)](https://packagist.org/packages/simtabi/lacommerce)
[![Tests](https://github.com/laranail/lacommerce/actions/workflows/laravel.yml/badge.svg)](https://github.com/laranail/lacommerce/actions/workflows/laravel.yml)
[![License: MIT](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE.md)

> Handy helper generators for Laravel e-commerce projects — auto-generate unique SKUs, order numbers, and ticket numbers on your Eloquent models via the `HasSku`, `HasOrderNumber`, and `HasTicketNumber` traits, each configurable globally or per model and fully replaceable.

Compatible with PHP `^8.0`.

## Install

```bash
composer require simtabi/lacommerce
php artisan vendor:publish --tag=lacommerce:config
```

## Documentation

Full documentation is at **[opensource.simtabi.com/documentation/laranail/lacommerce](https://opensource.simtabi.com/documentation/laranail/lacommerce/)** — installation, getting started, each generator trait, and configuration.

## Credits

The SKU-generation approach builds on [Cyrill Kalita / binary-cats](https://github.com/binary-cats)' work,
plus [all contributors](https://github.com/laranail/lacommerce/graphs/contributors).

## Contributing & security

Issues and PRs are welcome — see [CONTRIBUTING.md](CONTRIBUTING.md). Report vulnerabilities per
[SECURITY.md](SECURITY.md) (opensource@simtabi.com); participation follows the [Code of Conduct](CODE_OF_CONDUCT.md).

## License

MIT © Simtabi LLC. See [LICENSE.md](LICENSE.md).
