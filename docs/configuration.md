# Configuration

Every key in `config/lacommerce.php`, published with `vendor:publish --tag=lacommerce:config`. See the
[Documentation index](../README.md#documentation).

## Structure

Config lives under `generator`: a shared `default` block plus one block per generator (`sku`,
`ticket_number`, `order_number`).

```php
return [
    'generator' => [
        'default' => [
            'separator'          => '-',    // separator between source parts
            'unique'             => true,   // enforce uniqueness of the generated value
            'generate_on_create' => true,   // generate on model create
            'refresh_on_update'  => true,   // regenerate on model update
        ],

        'sku' => [
            'generator'   => SkuGenerator::class,           // must implement GeneratorInterface
            'source'      => 'name',                        // source column(s)
            'destination' => 'sku',                         // destination column
        ],

        'ticket_number' => [
            'generator'   => TicketNumberGenerator::class,
            'source'      => 'name',
            'destination' => 'ticket_number',
        ],

        'order_number' => [
            'generator'   => OrderNumberGenerator::class,
            'source'      => 'name',
            'destination' => 'order_number',
        ],
    ],
];
```

## Keys

| Key | Purpose |
|-----|---------|
| `generator.default.separator` | Joins multiple source values (default `-`). |
| `generator.default.unique` | Enforce the generated value is unique. |
| `generator.default.generate_on_create` | Generate when the model is created. |
| `generator.default.refresh_on_update` | Regenerate when the model is updated. |
| `generator.<name>.generator` | The generator class (must implement its `GeneratorInterface`). |
| `generator.<name>.source` | Source column(s) the value is derived from. |
| `generator.<name>.destination` | Column the generated value is written to. |

Override any of these per model via the trait's config method — see [Generators](tools/generators.md).

---

[← Docs index](../README.md#documentation)
