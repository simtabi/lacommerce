# Getting started

Generate your first SKU in three steps. See the [Documentation index](../README.md#documentation).

## 1. Install + publish

```bash
composer require simtabi/lacommerce
php artisan vendor:publish --tag=lacommerce:config
```

See [Installation](installation.md).

## 2. Add the trait

Add `HasSku` to a model that has an `sku` column:

```php
use Simtabi\Lacommerce\Traits\HasSku;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasSku;
}
```

## 3. Save — the SKU is generated

```php
$product = new Product();
$product->name = 'Laravel is Awesome';
$product->save();

echo $product->sku; // "LAR-80564492"
```

Order numbers and ticket numbers work the same way via `HasOrderNumber` / `HasTicketNumber`.

## Next steps

- [Generators](tools/generators.md) — all three generators, per-model config, custom generators.
- [Configuration](configuration.md) — every `config/lacommerce.php` key.

---

[← Docs index](../README.md#documentation)
