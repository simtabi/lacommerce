# simtabi/lacommerce

[![Latest version on Packagist](https://img.shields.io/packagist/v/simtabi/lacommerce.svg)](https://packagist.org/packages/simtabi/lacommerce)
[![Tests](https://github.com/laranail/lacommerce/actions/workflows/laravel.yml/badge.svg)](https://github.com/laranail/lacommerce/actions/workflows/laravel.yml)
[![License: MIT](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE.md)

> Handy helper generators for Laravel e-commerce projects — auto-generate unique SKUs, order numbers, and
> ticket numbers on your Eloquent models.

`simtabi/lacommerce` adds three sequence generators, each enabled by a trait and produced automatically on
save:

- **SKU** — `HasSku` → an `sku` column.
- **Order number** — `HasOrderNumber` → an `order_number` column.
- **Ticket number** — `HasTicketNumber` → a `ticket_number` column.

Each is configurable globally or per model, and fully replaceable with a custom generator. Compatible with
PHP `^8.0`.

## Install

```bash
composer require simtabi/lacommerce
php artisan vendor:publish --tag=lacommerce:config
```

See [Installation](docs/installation.md).

## Quick start

```php
use Simtabi\Lacommerce\Traits\HasSku;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasSku;
}

$product = new Product();
$product->name = 'Laravel is Awesome';
$product->save();

echo $product->sku; // "LAR-80564492"
```

Full walkthrough: [Getting started](docs/getting-started.md).

## <a name="documentation"></a>Documentation

Hosted at [`opensource.simtabi.com/lacommerce/docs/`](https://opensource.simtabi.com/lacommerce/docs/). The
same pages live under [`docs/`](docs/):

### Guides

- [Installation](docs/installation.md) — install, publish, requirements.
- [Getting started](docs/getting-started.md) — generate your first SKU.
- [Configuration](docs/configuration.md) — every `config/lacommerce.php` key.
- [Architecture](docs/architecture.md) — traits, observer, generator, configs.

### Reference

- [Generators](docs/tools/generators.md) — SKU / order-number / ticket-number generators, per-model config, custom generators.

### Project

- [Changelog](CHANGELOG.md) — release history.

## Stability

Pre-1.0 (0.x) — the public API may change between minor versions. Pin a version before bumping.

## Local development

```bash
composer install
composer test     # run the test suite
```

## Sister packages

- [`laranail/toolkit`](https://github.com/laranail/toolkit) — the broader Laravel utility toolkit.
- [`laranail/enumerator`](https://github.com/laranail/enumerator) — strongly-typed enums.

## Community

- [Issues](https://github.com/laranail/lacommerce/issues) — bugs + feature requests.

## Contributing & security

- [CONTRIBUTING.md](CONTRIBUTING.md) — workflow + coding standards.

## Credits

The SKU-generation approach builds on [Cyrill Kalita / binary-cats](https://github.com/binary-cats)' work,
plus [all contributors](https://github.com/laranail/lacommerce/contributors).

## License

MIT © Simtabi LLC. See [LICENSE.md](LICENSE.md).
