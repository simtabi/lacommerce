<?php

namespace Simtabi\Lacommerce\Supports;

use Illuminate\Support\Str;

class Helpers
{

    public static function makeRandomString(string $source, string $separator = '-'): string
    {
        // signature
        $signature = str_shuffle(str_repeat(str_pad('0123456789', 10, rand(0, 9).rand(0, 9), STR_PAD_LEFT), 2));

        // Sanitize the signature
        $signature = substr($signature, 0, 10);

        // Implode with random
        $result    = !empty($prefix) ? implode($separator, [$prefix, $source, $signature]) : implode($separator, [$source, $signature]);

        // Uppercase it
        return Str::upper($result);
    }

}
