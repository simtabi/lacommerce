<?php

declare(strict_types=1);

namespace Simtabi\Lacommerce\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Simtabi\Lacommerce\Supports\Helpers;

/**
 * makeRandomString() read a `$prefix` it never declared: the body was lifted out of the 0.1.0 `Str`
 * macros, where `$prefix` was a closure parameter, and the parameter was left behind. Because `empty()`
 * of an undefined variable raises nothing, every caller silently lost its prefix.
 */
final class HelpersTest extends TestCase
{
    #[Test]
    public function without_a_prefix_it_returns_the_source_and_ten_digits(): void
    {
        $this->assertMatchesRegularExpression('/^SRC-\d{10}$/', Helpers::makeRandomString('src'));
        $this->assertMatchesRegularExpression('/^SRC_\d{10}$/', Helpers::makeRandomString('src', '_'));
        $this->assertMatchesRegularExpression('/^SRC-\d{10}$/', Helpers::makeRandomString('src', '-', null));
    }

    #[Test]
    public function an_empty_prefix_is_the_same_as_none(): void
    {
        $this->assertMatchesRegularExpression('/^SRC-\d{10}$/', Helpers::makeRandomString('src', '-', ''));
    }

    #[Test]
    public function a_prefix_leads_the_value_and_is_joined_with_the_separator(): void
    {
        $this->assertMatchesRegularExpression('/^PFX-SRC-\d{10}$/', Helpers::makeRandomString('src', '-', 'pfx'));
        $this->assertMatchesRegularExpression('/^PFX\/SRC\/\d{10}$/', Helpers::makeRandomString('src', '/', 'PFX'));
    }

    #[Test]
    public function a_prefix_of_zero_is_kept(): void
    {
        // empty('0') is true, so the original check would have dropped it.
        $this->assertMatchesRegularExpression('/^0-SRC-\d{10}$/', Helpers::makeRandomString('src', '-', '0'));
    }
}
