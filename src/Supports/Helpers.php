<?php

namespace Simtabi\Lacommerce\Supports;

use Illuminate\Support\Str;

class Helpers
{

    /**
     * Join an optional prefix, the source and ten random digits with the separator, upper-cased.
     *
     * `makeRandomString('lar')` returns e.g. `LAR-8056449213`; `makeRandomString('lar', '-', 'acme')`
     * returns e.g. `ACME-LAR-8056449213`. A null or empty-string prefix adds no segment.
     *
     * @param  string       $source    the part after the prefix
     * @param  string       $separator joins the parts, used as given
     * @param  string|null  $prefix    an optional leading part
     * @return string
     */
    public static function makeRandomString(string $source, string $separator = '-', ?string $prefix = null): string
    {
        // signature
        $signature = str_shuffle(str_repeat(str_pad('0123456789', 10, rand(0, 9).rand(0, 9), STR_PAD_LEFT), 2));

        // Sanitize the signature
        $signature = substr($signature, 0, 10);

        // Implode with random
        $parts     = $prefix !== null && $prefix !== '' ? [$prefix, $source, $signature] : [$source, $signature];
        $result    = implode($separator, $parts);

        // Uppercase it
        return Str::upper($result);
    }

}
