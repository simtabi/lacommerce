# Configuration

Every key in `config/simtabi/lacommerce.php`, published with `vendor:publish --tag=simtabi::lacommerce-config`
and read from the `simtabi.lacommerce` config key. See the [Documentation index](../README.md#documentation).

## Structure

Config lives under `generator`, plus the top-level `register_legacy_macros` switch. `generator` holds a shared `default` block plus one block per generator (`sku`,
`ticket_number`, `order_number`).

```php
return [
    'register_legacy_macros' => true,   // register the deprecated bare Str macros

    'generator' => [
        'default' => [
            'separator'          => '-',    // separator between source parts
            'unique'             => true,   // enforce uniqueness of the generated value
            'generate_on_create' => true,   // generate on model create
            'refresh_on_update'  => true,   // regenerate on model update
        ],

        'sku' => [
            'generator'          => SkuGenerator::class,    // must implement GeneratorInterface
            'source_column'      => 'name',                 // source column(s)
            'destination_column' => 'sku',                  // destination column
            'prefix'             => null,                   // optional leading part
        ],

        'ticket_number' => [
            'generator'          => TicketNumberGenerator::class,
            'source_column'      => 'name',
            'destination_column' => 'ticket_number',
            'prefix'             => null,
        ],

        'order_number' => [
            'generator'          => OrderNumberGenerator::class,
            'source_column'      => 'name',
            'destination_column' => 'order_number',
            'prefix'             => null,                   // replaces the default ORD
        ],
    ],
];
```

## Keys

| Key | Purpose |
|-----|---------|
| `register_legacy_macros` | Register the deprecated bare `Str::sku()`, `Str::orderNumber()` and `Str::ticketNumber()` (default `true`). Set `false` to leave those names free; see [Generators](tools/generators.md#str-macros). |
| `generator.default.separator` | Joins multiple source values (default `-`). |
| `generator.default.unique` | Enforce the generated value is unique. |
| `generator.default.generate_on_create` | Generate when the model is created. |
| `generator.default.refresh_on_update` | Regenerate when the model is updated. |
| `generator.<name>.generator` | The generator class. Built as `new $class($model)`; it must implement `GeneratorInterface`, or the first save throws `InvalidOptionException`. See [Custom generators](tools/generators.md#custom-generators). |
| `generator.<name>.source_column` | Source column(s) the value is derived from. |
| `generator.<name>.destination_column` | Column the generated value is written to. |
| `generator.<name>.prefix` | Optional leading part of the value (default `null`, none). For `order_number` it replaces `ORD`. A published file without the key behaves as `null`. See [Prefixes](tools/generators.md#prefixes). |

Override any of these per model via the trait's config method — see [Generators](tools/generators.md).

The published file replaces the defaults block by block, not key by key: a top-level key you keep
(`generator`) must carry every nested key the package reads, so publish the whole file and edit it.

## The deprecated `lacommerce` key

Before 0.1.0 the config lived under the bare key `lacommerce`, published to `config/lacommerce.php` with
`--tag=lacommerce:config`. Both still work and are deprecated; the earliest release that could remove them
is 0.2.0.

- A published `config/lacommerce.php` is still read. When `config/simtabi/lacommerce.php` also exists, it
  wins.
- `config('lacommerce.*')` still returns the merged configuration.
- `--tag=lacommerce:config` still publishes `config/lacommerce.php`.

To migrate, move the file to `config/simtabi/lacommerce.php` and replace any `config('lacommerce.…')` reads
with `config('simtabi.lacommerce.…')`.

---

[← Docs index](../README.md#documentation)
