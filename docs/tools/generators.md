# Generators

The three sequence generators — SKU, order number, and ticket number — and how to configure, scope, and
replace them. See the [Documentation index](../README.md#documentation).

## The three generators

Each generator is enabled by adding its trait to an Eloquent model; the value is generated automatically on
save via a model observer:

| Generator | Trait | Default destination column |
|-----------|-------|----------------------------|
| SKU | `Simtabi\Lacommerce\Traits\HasSku` | `sku` |
| Order number | `Simtabi\Lacommerce\Traits\HasOrderNumber` | `order_number` |
| Ticket number | `Simtabi\Lacommerce\Traits\HasTicketNumber` | `ticket_number` |

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

The trait registers an observer for the destination field, generated every time you save the model. If you
plan to overwrite values manually, add the destination column to the model's `$fillable`.

`Illuminate\Support\Str::sku()` is also registered, so you can generate a value directly.

## Per-model configuration

Overload the config method (`skuConfigs()` / `orderNumberConfigs()` / `ticketNumberConfigs()`) to change
settings for a specific model:

```php
use Simtabi\Lacommerce\Generators\Concerns\Sku\SkuConfigs;
use Simtabi\Lacommerce\Traits\HasSku;

class Product extends Model
{
    use HasSku;

    public function skuConfigs(): SkuConfigs
    {
        return SkuConfigs::make()
            ->setSourceColumn(['id', 'user_id'])
            ->setDestinationColumn('order_number')
            ->setSeparator('-')
            ->forceUnique(true)
            ->generateOnCreate(true)
            ->refreshOnUpdate(false);
    }
}
```

## Custom generators

For extra logic (a default value, a prefix, …) extend the base generator and override `getSourceString()`:

```php
namespace App\Components\SkuGenerator;

use Simtabi\Lacommerce\Generators\Concerns\Sku\SkuGenerator;

class CustomSkuGenerator extends SkuGenerator
{
    protected function getSourceString(): string
    {
        $source = $this->configs->source;
        $fields = array_filter($this->model->only($source));

        if (empty($fields)) {
            return 'some-random-value-logic';
        }

        return implode($this->configs->separator, $fields);
    }
}
```

Then point the config at it:

```php
'generator' => \App\Components\SkuGenerator\CustomSkuGenerator::class,
```

A custom generator must implement `Simtabi\Lacommerce\SKU\Contracts\SkuGenerator`.

## About SKUs

A [Stock Keeping Unit](https://en.wikipedia.org/wiki/Stock_keeping_unit) is a unique identifier/code
referring to a particular stock-keeping unit.

---

[← Docs index](../../README.md#documentation)
