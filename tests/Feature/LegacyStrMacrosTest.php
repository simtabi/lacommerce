<?php

declare(strict_types=1);

namespace Simtabi\Lacommerce\Tests\Feature;

use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\WithoutErrorHandler;
use Simtabi\Lacommerce\Tests\TestCase;

/**
 * Pins the behaviour of the bare Str macros shipped in 0.1.0 (`Str::sku`, `Str::orderNumber`,
 * `Str::ticketNumber`). They are deprecated, not removed: their output format and arguments must not
 * change until the release that removes them.
 */
final class LegacyStrMacrosTest extends TestCase
{
    protected function setUp(): void
    {
        // Macros live in a static registry shared by every test in the process. Start each test from
        // an empty one so a macro registered by an earlier test cannot answer for this provider.
        Str::flushMacros();

        parent::setUp();
    }

    #[Test]
    public function the_bare_macros_are_registered_by_default(): void
    {
        $this->assertTrue(Str::hasMacro('sku'));
        $this->assertTrue(Str::hasMacro('orderNumber'));
        $this->assertTrue(Str::hasMacro('ticketNumber'));
    }

    #[Test]
    public function sku_takes_the_first_three_studly_characters_of_the_source(): void
    {
        $this->assertMatchesRegularExpression('/^LAR-\d{10}$/', @Str::sku('laravel is awesome'));
    }

    #[Test]
    public function sku_uses_the_separator_it_is_given(): void
    {
        $this->assertMatchesRegularExpression('/^LAR_\d{10}$/', @Str::sku('laravel', '_'));
    }

    #[Test]
    public function sku_falls_back_to_the_configured_separator_for_an_empty_one(): void
    {
        $this->assertMatchesRegularExpression('/^LAR-\d{10}$/', @Str::sku('laravel', ''));
        $this->assertMatchesRegularExpression('/^LAR-\d{10}$/', @Str::sku('laravel', null));
    }

    #[Test]
    public function sku_ignores_its_prefix_argument(): void
    {
        $this->assertMatchesRegularExpression('/^LAR-\d{10}$/', @Str::sku('laravel', '-', 'PFX'));
    }

    #[Test]
    public function order_number_ignores_its_source_and_defaults_to_ord(): void
    {
        $this->assertMatchesRegularExpression('/^ORD-\d{10}$/', @Str::orderNumber('Blue shirt'));
        $this->assertMatchesRegularExpression('/^ORD-\d{10}$/', @Str::orderNumber(null));
    }

    #[Test]
    public function order_number_uses_its_prefix_and_separator(): void
    {
        $this->assertMatchesRegularExpression('/^INV\/\d{10}$/', @Str::orderNumber('ignored', '/', 'inv'));
    }

    #[Test]
    public function ticket_number_takes_the_first_three_studly_characters_of_the_source(): void
    {
        $this->assertMatchesRegularExpression('/^SUP-\d{10}$/', @Str::ticketNumber('support request'));
        $this->assertMatchesRegularExpression('/^SUP\.\d{10}$/', @Str::ticketNumber('support', '.'));
    }

    #[Test]
    #[WithoutErrorHandler]
    public function each_bare_macro_raises_one_deprecation_per_boot_and_still_returns_a_value(): void
    {
        $raised = [];
        set_error_handler(static function (int $level, string $message) use (&$raised): bool {
            $raised[] = [$level, $message];

            return true;
        }, E_USER_DEPRECATED);

        try {
            $first  = Str::sku('laravel');
            $second = Str::sku('laravel');
            Str::orderNumber(null);
            Str::ticketNumber('support');
        } finally {
            restore_error_handler();
        }

        $this->assertMatchesRegularExpression('/^LAR-\d{10}$/', $first);
        $this->assertMatchesRegularExpression('/^LAR-\d{10}$/', $second);
        $this->assertCount(3, $raised);
        $this->assertSame(E_USER_DEPRECATED, $raised[0][0]);
        $this->assertStringContainsString('Str::sku()', $raised[0][1]);
        $this->assertStringContainsString('Identifiers::sku()', $raised[0][1]);
    }
}
