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

### Changed

- The `HasSku`, `HasOrderNumber` and `HasTicketNumber` generators call `Identifiers` directly instead of the
  bare `Str` macro. A package or application that registers its own `Str::sku()` no longer changes what
  your models generate. A custom generator naming another `$strMixin` still has that macro called.

### Deprecated

- The bare macros `Str::sku()`, `Str::orderNumber()` and `Str::ticketNumber()`. `Str`'s macro registry is one
  flat map, so any other registration of those names silently replaces them. They still work, with the
  same arguments and output, forwarding to the scoped macros, and raise one `E_USER_DEPRECATED` per name per
  boot. Earliest removal: 0.2.0.

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
