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

