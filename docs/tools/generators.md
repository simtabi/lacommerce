# Generators

The three sequence generators — SKU, order number, and ticket number — and how to configure, scope, and
replace them. See the [Documentation index](../../README.md#documentation).

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

echo $product->sku; // "LAR-8056449213"
```

The trait registers model-event listeners for the destination field, generated every time you save the model. If you
plan to overwrite values manually, add the destination column to the model's `$fillable`.

## Generating a value directly

`Simtabi\Lacommerce\Supports\Identifiers` generates a value without a model. It is a final class of three
static methods, and it goes through no registry, so nothing another package or your application registers
can change what it returns:

```php
use Simtabi\Lacommerce\Supports\Identifiers;

Identifiers::sku('Laravel is Awesome');        // "LAR-8056449213"
Identifiers::sku('Laravel is Awesome', '_');   // "LAR_8056449213"
Identifiers::orderNumber();                    // "ORD-3920571846"
Identifiers::orderNumber('INV', '/');          // "INV/3920571846"
Identifiers::ticketNumber('Support request');  // "SUP-1749302865"
```

| Method | Returns |
|--------|---------|
| `sku(string $source, string $separator = '-')` | First three characters of the studly-cased source, the separator, ten random digits, upper-cased. |
| `orderNumber(?string $prefix = null, string $separator = '-')` | The prefix (`ORD` when empty), the separator, ten random digits, upper-cased. |
| `ticketNumber(string $source, string $separator = '-')` | As `sku()`. |

The separator is used as given. The traits pass the configured `generator.default.separator`. A value from
`Identifiers` is not checked against your table; uniqueness is enforced by the traits' generators.

### `Str` macros

The same three are registered on `Illuminate\Support\Str` under vendor-and-package names. They read the
configured separator when you pass none:

| Macro | Forwards to |
|-------|-------------|
| `Str::simtabiLacommerceSku(string $source, ?string $separator = null)` | `Identifiers::sku()` |
| `Str::simtabiLacommerceOrderNumber(?string $source, ?string $separator = null, ?string $prefix = null)` | `Identifiers::orderNumber($prefix, …)`; `$source` is ignored |
| `Str::simtabiLacommerceTicketNumber(string $source, ?string $separator = null)` | `Identifiers::ticketNumber()` |

Each takes the same arguments as the bare macro it replaces, so migrating is a rename.

> The bare `Str::sku()`, `Str::orderNumber()` and `Str::ticketNumber()` from 0.1.0 are deprecated.
> `Str`'s macros are one flat map keyed by name, so another package or your application registering
> `Str::sku()` silently replaces this one, or this one replaces theirs. They still work, forward to the
> scoped macros with the same arguments, and raise one `E_USER_DEPRECATED` per name per boot. Set
> `register_legacy_macros` to `false` to stop registering them and leave the names free. The earliest release
> that could remove them is 0.2.0. Since this release, the traits no longer call any `Str` macro for the three
> shipped generators, so overriding `Str::sku()` does not change the SKUs your models get.

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
        $source = $this->modelConfig->sourceColumn;
        $fields = array_filter($this->model->only($source));

        if (empty($fields)) {
            return 'some-random-value-logic';
        }

        return implode($this->modelConfig->separator, $fields);
    }
}
```

Then point the config at it:

```php
'generator' => \App\Components\SkuGenerator\CustomSkuGenerator::class,
```

A custom generator must implement `Simtabi\Lacommerce\Generators\Contracts\SkuGeneratorInterface` (or the
`OrderNumberGeneratorInterface` / `TicketNumberGeneratorInterface` beside it); extending the shipped generator
does that for you.

## About SKUs

A [Stock Keeping Unit](https://en.wikipedia.org/wiki/Stock_keeping_unit) is a unique identifier/code
referring to a particular stock-keeping unit.

---

[← Docs index](../../README.md#documentation)
