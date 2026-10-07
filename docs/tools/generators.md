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
Identifiers::sku('Laravel is Awesome', '-', 'acme'); // "ACME-LAR-8056449213"
Identifiers::orderNumber();                    // "ORD-3920571846"
Identifiers::orderNumber('INV', '/');          // "INV/3920571846"
Identifiers::ticketNumber('Support request');  // "SUP-1749302865"
```

| Method | Returns |
|--------|---------|
| `sku(string $source, string $separator = '-', ?string $prefix = null)` | The prefix when given, then the first three characters of the studly-cased source, then ten random digits, joined by the separator and upper-cased. |
| `orderNumber(?string $prefix = null, string $separator = '-')` | The prefix (`ORD` when empty), the separator, ten random digits, upper-cased. |
| `ticketNumber(string $source, string $separator = '-', ?string $prefix = null)` | As `sku()`. |

The separator is used as given. The traits pass the configured `generator.default.separator`, and the
`prefix` from the generator's config block (see [Prefixes](#prefixes)). A value from
`Identifiers` is not checked against your table; uniqueness is enforced by the traits' generators.

### `Str` macros

The same three are registered on `Illuminate\Support\Str` under vendor-and-package names. They read the
configured separator when you pass none:

| Macro | Forwards to |
|-------|-------------|
| `Str::simtabiLacommerceSku(string $source, ?string $separator = null, ?string $prefix = null)` | `Identifiers::sku()` |
| `Str::simtabiLacommerceOrderNumber(?string $source, ?string $separator = null, ?string $prefix = null)` | `Identifiers::orderNumber($prefix, …)`; `$source` is ignored |
| `Str::simtabiLacommerceTicketNumber(string $source, ?string $separator = null, ?string $prefix = null)` | `Identifiers::ticketNumber()` |

Each takes the same arguments as the bare macro it replaces, so migrating is a rename. The `sku` and
`ticketNumber` macros pass their third `$prefix` argument through, so `Str::simtabiLacommerceSku('laravel',
'-', 'acme')` returns e.g. `ACME-LAR-8056449213`. The 0.1.0 macros accepted it and dropped it.

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
            ->setDestinationColumn('sku')
            ->setPrefix('ACME')
            ->setSeparator('-')
            ->forceUnique(true)
            ->generateOnCreate(true)
            ->refreshOnUpdate(false);
    }
}
```

## Prefixes

Each generator's config block takes a `prefix`, `null` by default. When set, it leads the value:

| Block | `prefix` | Value |
|-------|----------|-------|
| `sku` | `'acme'` | `ACME-BLU-8056449213` |
| `ticket_number` | `'HD'` | `HD-PRI-1749302865` |
| `order_number` | `'INV'` | `INV-3920571846`; the prefix replaces the default `ORD` |

`setPrefix()` on a model's configs sets it per model, as in the example above. `getPrefix()` returns `null`
when none is set.

## Custom generators

The shipped `SkuGenerator`, `OrderNumberGenerator` and `TicketNumberGenerator` are final, so they are not the
thing to extend. The provider builds whichever class a config block's `generator` key names as
`new $class($model)`, so a custom generator needs two things:

- a constructor that takes the model as its only argument, and
- `Simtabi\Lacommerce\Generators\Services\Contracts\GeneratorInterface`, whose one method, `render()`,
  returns the value. Implement the per-type interface beside it in `Generators\Contracts`
  (`SkuGeneratorInterface`, `OrderNumberGeneratorInterface`, `TicketNumberGeneratorInterface`), which extends
  it, so the class is what the container says it resolves.

A class naming anything else fails on the first save with an `InvalidOptionException` that names the config
key.

### Extend the base generator

`Simtabi\Lacommerce\Generators\Services\Generator` is the base the shipped generators extend, and it is
built to be extended. Its parent constructor takes the model, the trait's config method (`skuConfigs`,
`orderNumberConfigs` or `ticketNumberConfigs`) and the identifier kind (`sku`, `orderNumber` or
`ticketNumber`). `render()` builds the source with `getSourceString()`, makes a candidate with `makeValue()`,
and, when `unique` is on, retries while `exists()` finds the candidate in the destination column. Override
whichever of those protected methods you need, and the rest, uniqueness included, keeps working.

This one falls back to a fixed source when the model's source columns are empty:

```php
namespace App\Generators;

use Illuminate\Database\Eloquent\Model;
use Simtabi\Lacommerce\Generators\Contracts\SkuGeneratorInterface;
use Simtabi\Lacommerce\Generators\Services\Generator;

final class FallbackSkuGenerator extends Generator implements SkuGeneratorInterface
{
    public function __construct(Model $model)
    {
        parent::__construct($model, 'skuConfigs', 'sku');
    }

    protected function getSourceString(): string
    {
        $fields = array_filter($this->model->only($this->modelConfig->getSourceColumn()));

        if ($fields === []) {
            return 'item';
        }

        return implode($this->modelConfig->getSeparator(), $fields);
    }
}
```

A product named `Blue shirt` still gets `BLU-8056449213`; one with an empty name gets `ITE-8056449213`.

### Implement the interface

When the value has nothing to do with the shipped format, implement the interface directly. You then own
uniqueness:

```php
namespace App\Generators;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Simtabi\Lacommerce\Generators\Contracts\TicketNumberGeneratorInterface;

final class SlugTicketNumberGenerator implements TicketNumberGeneratorInterface
{
    public function __construct(private readonly Model $model)
    {
    }

    public function render(): string
    {
        return 'TKT-' . Str::upper(Str::slug((string) $this->model->getAttribute('name')));
    }
}
```

### Configure it

Name the class in the published `config/simtabi/lacommerce.php`:

```php
'generator' => [
    // ...
    'sku' => [
        'generator'          => \App\Generators\FallbackSkuGenerator::class,
        'source_column'      => 'name',
        'destination_column' => 'sku',
        'prefix'             => null,
    ],
],
```

Both examples are the package's own test fixtures, run against a model on every build, and a test fails if
the first one here stops matching its fixture.

## About SKUs

A [Stock Keeping Unit](https://en.wikipedia.org/wiki/Stock_keeping_unit) is a unique identifier/code
referring to a particular stock-keeping unit.

---

[← Docs index](../../README.md#documentation)
