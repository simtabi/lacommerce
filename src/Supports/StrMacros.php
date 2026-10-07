<?php

declare(strict_types=1);

namespace Simtabi\Lacommerce\Supports;

use Closure;
use Illuminate\Support\Str;

/**
 * Registers the package's `Str` macros.
 *
 * `Str`'s macro registry is a flat static map keyed by name, so a second registration of a name
 * silently replaces the first. The macros are therefore registered under vendor-and-package names
 * (`Str::simtabiLacommerceSku()`); a macro name is called as a PHP method, so it cannot hold the
 * slash or hyphen the other registries use, and camel case is the only shape that stays callable.
 *
 * The bare names shipped in 0.1.0 (`Str::sku()`, `Str::orderNumber()`, `Str::ticketNumber()`) are
 * still registered, unless `simtabi.lacommerce.register_legacy_macros` is false, and forward to the
 * scoped macro with the same arguments. A `$prefix` passed to the SKU or ticket-number macro leads the
 * value (`PFX-LAR-8056449213`); 0.1.0 accepted it and dropped it.
 */
final class StrMacros
{
    public const SKU = 'simtabiLacommerceSku';

    public const ORDER_NUMBER = 'simtabiLacommerceOrderNumber';

    public const TICKET_NUMBER = 'simtabiLacommerceTicketNumber';

    /**
     * Bare macro name => the vendor-scoped macro it forwards to.
     *
     * @deprecated The bare macros shipped in 0.1.0 are kept so their callers keep working. Call
     *             Identifiers, or the scoped macros, instead. Earliest removal: 0.2.0.
     */
    public const DEPRECATED_ALIASES = [
        'sku'          => self::SKU,
        'orderNumber'  => self::ORDER_NUMBER,
        'ticketNumber' => self::TICKET_NUMBER,
    ];

    /**
     * Bare name => the Identifiers method that replaces it, for the deprecation message.
     */
    private const REPLACEMENTS = [
        'sku'          => 'sku',
        'orderNumber'  => 'orderNumber',
        'ticketNumber' => 'ticketNumber',
    ];

    /**
     * @var array<string, true> bare names that have already raised their deprecation since the last
     *                          register(), which runs once per application boot
     */
    private static array $warned = [];

    /**
     * @param string $defaultSeparator used when a caller passes no separator, or an empty one
     * @param bool   $registerLegacy   whether to register the deprecated bare names
     */
    public static function register(string $defaultSeparator, bool $registerLegacy = true): void
    {
        self::$warned = [];

        Str::macro(self::SKU, static function (string $source, ?string $separator = null, ?string $prefix = null) use ($defaultSeparator): string {
            // 0.1.0 accepted $prefix and dropped it; it leads the value again, as it did before 2022.
            return Identifiers::sku($source, $separator ?: $defaultSeparator, $prefix);
        });

        Str::macro(self::ORDER_NUMBER, static function (?string $source, ?string $separator = null, ?string $prefix = null) use ($defaultSeparator): string {
            // $source is accepted and ignored, as it was by the 0.1.0 Str::orderNumber() this replaces.
            return Identifiers::orderNumber($prefix, $separator ?: $defaultSeparator);
        });

        Str::macro(self::TICKET_NUMBER, static function (string $source, ?string $separator = null, ?string $prefix = null) use ($defaultSeparator): string {
            // 0.1.0 accepted $prefix and dropped it; it leads the value again, as it did before 2022.
            return Identifiers::ticketNumber($source, $separator ?: $defaultSeparator, $prefix);
        });

        if ($registerLegacy) {
            foreach (array_keys(self::DEPRECATED_ALIASES) as $bare) {
                Str::macro($bare, self::deprecatedAlias($bare));
            }
        }
    }

    /**
     * Run a deprecated bare macro: raise its deprecation once per boot, then call the scoped macro.
     *
     * Public only because Macroable rebinds a macro closure to Str's scope, from which a private
     * member of this class is unreachable. Not part of the package's API.
     *
     * @internal
     * @deprecated Exists only for the bare macros. Earliest removal: 0.2.0.
     *
     * @param  array<int|string, mixed>  $arguments
     */
    public static function forwardDeprecated(string $bare, array $arguments): string
    {
        $scoped = self::DEPRECATED_ALIASES[$bare];

        if (! isset(self::$warned[$bare])) {
            self::$warned[$bare] = true;

            trigger_error(sprintf(
                'Str::%s() from simtabi/lacommerce is deprecated and may be removed in 0.2.0; '
                . 'use %s::%s() or Str::%s() instead.',
                $bare,
                Identifiers::class,
                self::REPLACEMENTS[$bare],
                $scoped,
            ), E_USER_DEPRECATED);
        }

        return Str::{$scoped}(...$arguments);
    }

    private static function deprecatedAlias(string $bare): Closure
    {
        // Macroable binds the closure to Str's scope, so name this class rather than using self::.
        return static fn (mixed ...$arguments): string => StrMacros::forwardDeprecated($bare, $arguments);
    }
}
