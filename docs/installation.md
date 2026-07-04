# Installation

Install `simtabi/lacommerce` and publish its config. See the [Documentation index](../README.md#documentation).

## Requirements

- PHP `^8.0`
- Laravel (the `LacommerceServiceProvider` is auto-discovered)

## Install

```bash
composer require simtabi/lacommerce
```

The service provider registers itself. Publish the config (and optionally assets/views):

```bash
php artisan vendor:publish --tag=lacommerce:config
php artisan vendor:publish --tag=lacommerce:assets
php artisan vendor:publish --tag=lacommerce:views
```

This publishes `config/lacommerce.php` — see [Configuration](configuration.md).

> Your model needs the destination column (e.g. `sku`) on its table. If you overwrite generated values
> manually, add that column to the model's `$fillable`.

## Next steps

- [Getting started](getting-started.md) — generate your first SKU.
- [Generators](tools/generators.md) — SKU, order-number, and ticket-number generators.

---

[← Docs index](../README.md#documentation)
