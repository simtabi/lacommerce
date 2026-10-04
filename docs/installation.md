# Installation

Install `simtabi/lacommerce` and publish its config. See the [Documentation index](../README.md#documentation).

## Requirements

- PHP `^8.4.1 || ^8.5`
- Laravel 12 or 13 (the `LacommerceServiceProvider` is auto-discovered)

## Install

```bash
composer require simtabi/lacommerce
```

The service provider registers itself. Publish the config if you want to change the defaults:

```bash
php artisan vendor:publish --tag=simtabi::lacommerce-config
```

This publishes `config/simtabi/lacommerce.php` — see [Configuration](configuration.md). The package ships
no views, translations or public assets, so there is nothing else to publish.

The package runs no migrations of its own: add the destination column to your own table.

> Your model needs the destination column (e.g. `sku`) on its table. If you overwrite generated values
> manually, add that column to the model's `$fillable`.

## Next steps

- [Getting started](getting-started.md) — generate your first SKU.
- [Generators](tools/generators.md) — SKU, order-number, and ticket-number generators.

---

[← Docs index](../README.md#documentation)
