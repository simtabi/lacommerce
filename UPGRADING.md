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
- A generator naming its own `Str` macro as `$strMixin` has it called as `($source, $separator, $prefix)`, with
  the configured prefix or `null`. It was called with two arguments. Check the macro's third parameter, if it
  has one.

### Prefixes

Each generator block takes a new `prefix` key. A config published before this release does not have it, which
is the same as `null`: values are generated exactly as before. Add it to the block to prefix the values.
`Identifiers::sku()` and `Identifiers::ticketNumber()` take the prefix as an optional third argument.

### The `Str` macros honour `$prefix` for SKUs and ticket numbers

**This changes output.** In 0.1.0, `Str::sku()` and `Str::ticketNumber()` accepted a third `$prefix` argument
and dropped it. They, and the scoped `Str::simtabiLacommerceSku()` and `Str::simtabiLacommerceTicketNumber()`,
now put it first:

| Call | 0.1.0 | Now |
|------|-------|-----|
| `Str::sku('laravel', '-', 'pfx')` | `LAR-8056449213` | `PFX-LAR-8056449213` |
| `Str::ticketNumber('support', '-', 'hd')` | `SUP-8056449213` | `HD-SUP-8056449213` |

A call passing no prefix, `null` or `''` is unchanged. If you passed a prefix and relied on it being dropped,
stop passing it. `Str::orderNumber()` always used its prefix and is unchanged.

### `setPrefix()` takes `?string`

`ConfigsInterface::setPrefix()` took `string` and `Configs::setPrefix()` took `mixed`; both take `?string` now.

- If you implement `ConfigsInterface` yourself, change your `setPrefix(string $prefix)` to
  `setPrefix(?string $prefix)`, or PHP refuses to load the class.
- If you call `setPrefix()` with an `int` or another non-string from a file declaring `strict_types=1`, cast
  it to a string first. A numeric `prefix` in the config file still works.

### Runtime config changes now apply

The generator and configs bindings used to read `simtabi.lacommerce.generator` once, at boot. They read it
when they resolve now, so `config()->set('simtabi.lacommerce.generator.sku.prefix', 'X')` after boot changes
the next SKU generated. If code changed that config at runtime expecting nothing to happen, it now takes effect.
