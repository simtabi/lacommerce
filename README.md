# simtabi/lacommerce

[![Tests](https://github.com/simtabi/lacommerce/actions/workflows/tests.yml/badge.svg)](https://github.com/simtabi/lacommerce/actions/workflows/tests.yml)
[![License: MIT](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

Two badges, not four: the package is not on Packagist yet, so there is no registry version to show, and
there is no static-analysis workflow.

> Handy helper generators for Laravel e-commerce projects — auto-generate unique SKUs, order numbers, and ticket numbers on your Eloquent models via the `HasSku`, `HasOrderNumber`, and `HasTicketNumber` traits, each configurable globally or per model and fully replaceable.

Compatible with PHP `^8.4.1 || ^8.5` and Laravel 12 or 13.

## Install

```bash
composer require simtabi/lacommerce
```

## Quick start guide and usage

### Getting started

The service provider is auto-discovered and the defaults work without a config file. Give each model the
destination column its trait writes to (`sku`, `order_number` or `ticket_number`) in your own migration.
To change the defaults, publish the config to `config/simtabi/lacommerce.php`:

```bash
php artisan vendor:publish --tag=simtabi::lacommerce-config
```

### Usage

```php
use Illuminate\Database\Eloquent\Model;
use Simtabi\Lacommerce\Traits\HasSku;

class Product extends Model
{
    use HasSku;
}

$product = Product::create(['name' => 'Laravel is Awesome']);

echo $product->sku; // "LAR-8056449213"
```

```php
use Illuminate\Database\Eloquent\Model;
use Simtabi\Lacommerce\Traits\HasOrderNumber;

class Order extends Model
{
    use HasOrderNumber;
}

echo Order::create(['name' => 'Web order'])->order_number; // "ORD-3920571846"
```

To generate a value without a model, call `Identifiers` directly:

```php
use Simtabi\Lacommerce\Supports\Identifiers;

Identifiers::sku('Laravel is Awesome');    // "LAR-8056449213"
Identifiers::orderNumber('INV', '/');      // "INV/3920571846"
Identifiers::ticketNumber('Support');      // "SUP-1749302865"
```

The full walkthrough is in [docs/getting-started.md](docs/getting-started.md); everything else is in the
[Documentation](#documentation) index.

## <a name="documentation"></a>Documentation

Full documentation is at **[opensource.simtabi.com/documentation/simtabi/lacommerce](https://opensource.simtabi.com/documentation/simtabi/lacommerce/)** — installation, getting started, each generator trait, and configuration.

## Credits

The SKU-generation approach builds on [Cyrill Kalita / binary-cats](https://github.com/binary-cats)' work,
plus [all contributors](https://github.com/simtabi/lacommerce/graphs/contributors).

## Contributing & security

Issues and PRs are welcome — see [CONTRIBUTING.md](CONTRIBUTING.md). Report vulnerabilities per
[SECURITY.md](SECURITY.md) (security@simtabi.com); participation follows the [Code of Conduct](CODE_OF_CONDUCT.md).

## License

MIT © Simtabi LLC. See [LICENSE](LICENSE).
