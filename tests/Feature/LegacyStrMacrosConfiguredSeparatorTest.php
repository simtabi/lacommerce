<?php

declare(strict_types=1);

namespace Simtabi\Lacommerce\Tests\Feature;

use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Simtabi\Lacommerce\Tests\TestCase;

/**
 * The bare macros and their vendor-scoped replacements default to the configured separator.
 */
final class LegacyStrMacrosConfiguredSeparatorTest extends TestCase
{
    protected function setUp(): void
    {
        Str::flushMacros();

        parent::setUp();
    }

    protected function publishedConfig(): array
    {
        $scoped = self::defaultConfig();
        $scoped['generator']['default']['separator'] = '/';

        return ['simtabi.lacommerce' => $scoped];
    }

    #[Test]
    public function the_bare_macros_use_the_configured_separator(): void
    {
        $this->assertMatchesRegularExpression('/^LAR\/\d{10}$/', @Str::sku('laravel'));
        $this->assertMatchesRegularExpression('/^ORD\/\d{10}$/', @Str::orderNumber(null));
        $this->assertMatchesRegularExpression('/^SUP\/\d{10}$/', @Str::ticketNumber('support'));
    }

    #[Test]
    public function the_scoped_macros_use_the_configured_separator(): void
    {
        $this->assertMatchesRegularExpression('/^LAR\/\d{10}$/', Str::simtabiLacommerceSku('laravel'));
        $this->assertMatchesRegularExpression('/^ORD\/\d{10}$/', Str::simtabiLacommerceOrderNumber(null));
        $this->assertMatchesRegularExpression('/^SUP\/\d{10}$/', Str::simtabiLacommerceTicketNumber('support'));
    }
}
