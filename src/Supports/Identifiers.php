<?php

declare(strict_types=1);

namespace Simtabi\Lacommerce\Supports;

use Illuminate\Support\Str;

/**
 * Generates SKUs, order numbers and ticket numbers without going through a host-owned registry.
 *
 * This is the package's primary API for generating an identifier directly. The `HasSku`,
 * `HasOrderNumber` and `HasTicketNumber` traits use it through their generators, and the
 * `Str::simtabiLacommerce*()` macros forward to it. Calling it directly means another package or the
 * host application registering a `Str` macro of the same name cannot change what it returns.
 *
 * Each value is an optional prefix, a source part and ten random digits, joined by the separator and
 * upper-cased: `Identifiers::sku('laravel is awesome')` returns e.g. `LAR-8056449213`, and
 * `Identifiers::sku('laravel is awesome', '-', 'acme')` returns e.g. `ACME-LAR-8056449213`. Uniqueness
 * against a table is the generator's job, not this class's.
 */
final class Identifiers
{
    public const DEFAULT_SEPARATOR = '-';

    public const DEFAULT_ORDER_PREFIX = 'ORD';

    /**
     * A SKU from the first three characters of the studly-cased source, led by the prefix when one is given.
     */
    public static function sku(string $source, string $separator = self::DEFAULT_SEPARATOR, ?string $prefix = null): string
    {
        return Helpers::makeRandomString(self::sourcePart($source), $separator, $prefix);
    }

    /**
     * An order number: the prefix (`ORD` when empty), the separator and ten random digits.
     */
    public static function orderNumber(?string $prefix = null, string $separator = self::DEFAULT_SEPARATOR): string
    {
        return Helpers::makeRandomString(! empty($prefix) ? $prefix : self::DEFAULT_ORDER_PREFIX, $separator);
    }

    /**
     * A ticket number from the first three characters of the studly-cased source, led by the prefix when
     * one is given.
     */
    public static function ticketNumber(string $source, string $separator = self::DEFAULT_SEPARATOR, ?string $prefix = null): string
    {
        return Helpers::makeRandomString(self::sourcePart($source), $separator, $prefix);
    }

    private static function sourcePart(string $source): string
    {
        return Str::limit(Str::studly($source), 3, '');
    }
}
