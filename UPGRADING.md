# Upgrading

## From 0.1.0

### The bare `Str` macros are deprecated

`Str::sku()`, `Str::orderNumber()` and `Str::ticketNumber()` still work and are deprecated. Replace them with
`Simtabi\Lacommerce\Supports\Identifiers`, or rename them to the scoped macros, which take the same
arguments:

| Before | After |
|--------|-------|
| `Str::sku($name)` | `Identifiers::sku($name)` or `Str::simtabiLacommerceSku($name)` |
| `Str::orderNumber(null, '-', 'INV')` | `Identifiers::orderNumber('INV', '-')` or `Str::simtabiLacommerceOrderNumber(null, '-', 'INV')` |
| `Str::ticketNumber($subject)` | `Identifiers::ticketNumber($subject)` or `Str::simtabiLacommerceTicketNumber($subject)` |

`Identifiers` uses the separator you pass, and `-` when you pass none; the macros fall back to the
configured `generator.default.separator`. Once nothing calls the bare names, set `register_legacy_macros` to
`false` in `config/simtabi/lacommerce.php`.

### Custom generators

The documentation used to tell you to extend `SkuGenerator`. It is final, so no working code did that. Extend
`Simtabi\Lacommerce\Generators\Services\Generator` instead, or implement the interface; see
[Custom generators](docs/tools/generators.md#custom-generators).

Two things changed for a custom generator that already works:

- The observer calls `render()` rather than casting the generator to a string. A class whose `__toString()`
  returned something other than `render()` now has `render()` used.
- A `generator` key naming a class that does not implement `GeneratorInterface` throws
  `InvalidOptionException` naming the key, instead of a `TypeError`.

### Prefixes

Each generator block takes a new `prefix` key. A config published before this release does not have it, which
is the same as `null`: values are generated exactly as before. Add it to the block to prefix the values.
`Identifiers::sku()` and `Identifiers::ticketNumber()` take the prefix as an optional third argument. The `Str`
macros still ignore their `$prefix` argument for SKUs and ticket numbers, as 0.1.0 did.

