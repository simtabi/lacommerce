# Changelog

All notable changes to `simtabi/lacommerce` are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project adheres to
[Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

- `Simtabi\Lacommerce\Supports\Identifiers`: `sku()`, `orderNumber()` and `ticketNumber()` as a final class
  that goes through no registry. It is now the primary way to generate a value without a model.
- Vendor-scoped `Str` macros `Str::simtabiLacommerceSku()`, `Str::simtabiLacommerceOrderNumber()` and
  `Str::simtabiLacommerceTicketNumber()`, taking the same arguments as the bare macros they replace.
- Config key `register_legacy_macros` (default `true`). Set it to `false` to stop registering the bare macros.
- Config key `generator.<name>.prefix` (default `null`) on the `sku`, `ticket_number` and `order_number` blocks.
  When set, it leads every value the trait generates: `ACME-BLU-8056449213`. For order numbers it replaces the
  default `ORD`. A published config without the key behaves as `null`.
- An optional `$prefix` third argument on `Identifiers::sku()` and `Identifiers::ticketNumber()`, and on
  `Supports\Helpers::makeRandomString()`.
- The shipped `SkuGenerator`, `OrderNumberGenerator` and `TicketNumberGenerator` implement the per-type
  interface (`SkuGeneratorInterface`, …) the container resolves them through. They extended only the base
  `GeneratorInterface` before.

### Changed

- The `HasSku`, `HasOrderNumber` and `HasTicketNumber` generators call `Identifiers` directly instead of the
  bare `Str` macro. A package or application that registers its own `Str::sku()` no longer changes what
  your models generate. A custom generator naming another `$strMixin` still has that macro called.
- The observer calls `render()` on the generator instead of casting it to a string. Output is unchanged for the
  shipped generators, whose `__toString()` returned `render()`.
- A `generator` config key naming a class that does not implement `GeneratorInterface` now throws
  `InvalidOptionException` naming the key on the first save. It threw a `TypeError` from the observer before.
- `InvalidOptionException::invalidArgument()` takes an optional `$code` (default `500`), and `render()` takes
  `Illuminate\Http\Request`. It used to drop the code its one caller passed, leaving `0`, and type-hint the
  `Request` facade, which a real request never is.

- The `sku` and `ticketNumber` `Str` macros, scoped and bare, pass their `$prefix` argument through to
  `Identifiers`. 0.1.0 accepted it and dropped it, which commit `5a0461b` (2022) introduced when it moved the
  bodies out of the macros; before that the prefix was honoured. A call that passed a prefix now gets it as the
  leading segment: `Str::sku('laravel', '-', 'pfx')` returned `LAR-8056449213` and now returns
  `PFX-LAR-8056449213`. Calls without a prefix, or with `null` or `''`, are unchanged.

- `ConfigsInterface::setPrefix()` and `Configs::setPrefix()` both take `?string`. The interface took `string`,
  refusing the `null` that `getPrefix()` returns, and the class took `mixed`. A class implementing
  `ConfigsInterface` itself must widen its parameter to `?string`. Calling `Configs::setPrefix()` from a file with
  `declare(strict_types=1)` with a non-string now throws a `TypeError` at the call; a numeric `prefix` in the
  config file still works.

### Deprecated

- The bare macros `Str::sku()`, `Str::orderNumber()` and `Str::ticketNumber()`. `Str`'s macro registry is one
  flat map, so any other registration of those names silently replaces them. They still work, with the
  same arguments and output, forwarding to the scoped macros, and raise one `E_USER_DEPRECATED` per name per
  boot. Earliest removal: 0.2.0.

### Fixed

- `Supports\Helpers::makeRandomString()` read a `$prefix` variable it never declared. The body was moved out of
  the 0.1.0 `Str` macros in 2022, where `$prefix` was a closure parameter, and the parameter was left behind.
  `empty()` of an undefined variable raises nothing, so the prefix branch could never run. It is now a third
  parameter, `?string $prefix = null`. Output for every existing call is unchanged: no caller passed a prefix.
  A call that does gets `PREFIX-SOURCE-DIGITS`, upper-cased. A prefix of `'0'` is kept; `null` and `''` add
  nothing.
- `docs/tools/generators.md` told you to extend `SkuGenerator`, which is final, so its custom-generator example
  could not compile. It now documents the two seams that work, extending the non-final
  `Generators\Services\Generator` base or implementing the interface, and both examples are test fixtures run
  against a model. A test fails if the documented example stops matching its fixture.
- A custom generator that implemented `GeneratorInterface` without a `__toString()` threw
  `Object ... could not be converted to string` on every save.
- `Configs::make()` resolved the base `Configs` class, which the container cannot build, so calling it on the
  base, or on a subclass that inherited it, threw `BindingResolutionException` about an unresolvable
  `array $config`. It now resolves the class it is called on (`static::class`), and on the base class throws
  `InvalidOptionException` naming the subclasses to call instead. `SkuConfigs::make()` and its siblings are
  unchanged.
- `Configs::getPrefix()`, and `skuConfig('prefix')` and its siblings, threw "must not be accessed before
  initialization" unless `setPrefix()` had been called. The prefix now defaults to `null`.

## [0.1.0] - 2026-10-05

The first tagged release. An entry dated 2022-02-03 used to sit here as `0.1.0`, but no tag was ever
cut for it and the package was never published.

### Added

- A Testbench suite covering the `HasSku`, `HasOrderNumber` and `HasTicketNumber` traits and the service
  provider, run in CI on PHP 8.4 and 8.5 against lowest and stable dependencies.
- Vendor-scoped config key `simtabi.lacommerce`, published to `config/simtabi/lacommerce.php` with the tag
  `simtabi::lacommerce-config`.
- `composer.json` now requires the `illuminate/*` components the source uses.

### Changed

- Requires PHP `^8.4.1 || ^8.5` and Laravel 12 or 13 (was PHP `^8.0`, no Laravel constraint).
- Package metadata, badges and documentation links point at the `simtabi` org.

### Deprecated

- The bare config key `lacommerce` and publish tag `lacommerce:config`. Both still work: a published
  `config/lacommerce.php` is still read (the scoped file wins when both exist), `config('lacommerce.*')`
  still returns the merged configuration, and the bare tag still publishes `config/lacommerce.php`.
  Earliest removal: 0.2.0.

### Fixed

- The traits failed on every model under current Laravel: they called `static::observe()` while the model
  was booting, which throws. They now register the observer's handlers directly.
- The provider loaded the package's `database/migrations` into every host application. That directory only
  held a test fixture creating a `dummy_models` table; the fixture now lives under `tests/` and nothing is
  loaded.
- The config was merged twice; it is merged once.
- View, translation and asset loaders and publish tags pointed at directories the package does not ship;
  they are now registered only when the directory exists.

[Unreleased]: https://github.com/simtabi/lacommerce/compare/v0.1.0...HEAD
[0.1.0]: https://github.com/simtabi/lacommerce/releases/tag/v0.1.0
