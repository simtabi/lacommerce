<?php

declare(strict_types=1);

namespace Simtabi\Lacommerce\Tests\Feature;

use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\WithoutErrorHandler;
use Simtabi\Lacommerce\Supports\StrMacros;
use Simtabi\Lacommerce\Tests\Fixtures\Models\DummyProduct;
use Simtabi\Lacommerce\Tests\TestCase;

final class ScopedStrMacrosTest extends TestCase
{
    protected function setUp(): void
    {
        Str::flushMacros();

        parent::setUp();
    }

    #[Test]
    public function the_scoped_macros_are_registered_under_the_vendor_and_package_name(): void
    {
        $this->assertSame([
            'sku'          => 'simtabiLacommerceSku',
            'orderNumber'  => 'simtabiLacommerceOrderNumber',
            'ticketNumber' => 'simtabiLacommerceTicketNumber',
        ], StrMacros::DEPRECATED_ALIASES);

        foreach (StrMacros::DEPRECATED_ALIASES as $scoped) {
            $this->assertTrue(Str::hasMacro($scoped), "Str::{$scoped}() is not registered");
        }
    }

    #[Test]
    public function no_scoped_name_shadows_a_real_str_method(): void
    {
        // Str::__callStatic only consults the macro map for a name that is not a real method, so a
        // macro whose name Laravel later adds as a method would silently stop being called.
        foreach ([...array_keys(StrMacros::DEPRECATED_ALIASES), ...StrMacros::DEPRECATED_ALIASES] as $name) {
            $this->assertFalse(method_exists(Str::class, $name), "Str::{$name}() is a real method");
        }
    }

    #[Test]
    public function the_scoped_macros_keep_the_bare_signatures(): void
    {
        $this->assertMatchesRegularExpression('/^LAR-\d{10}$/', Str::simtabiLacommerceSku('laravel is awesome'));
        $this->assertMatchesRegularExpression('/^LAR_\d{10}$/', Str::simtabiLacommerceSku('laravel', '_', 'ignored'));
        $this->assertMatchesRegularExpression('/^LAR-\d{10}$/', Str::simtabiLacommerceSku('laravel', ''));
        $this->assertMatchesRegularExpression('/^ORD-\d{10}$/', Str::simtabiLacommerceOrderNumber('ignored'));
        $this->assertMatchesRegularExpression('/^INV\/\d{10}$/', Str::simtabiLacommerceOrderNumber(null, '/', 'inv'));
        $this->assertMatchesRegularExpression('/^SUP-\d{10}$/', Str::simtabiLacommerceTicketNumber('support request'));
    }

    #[Test]
    #[WithoutErrorHandler]
    public function the_scoped_macros_raise_no_deprecation(): void
    {
        $raised = 0;
        set_error_handler(static function () use (&$raised): bool {
            $raised++;

            return true;
        }, E_USER_DEPRECATED);

        try {
            Str::simtabiLacommerceSku('laravel');
            Str::simtabiLacommerceOrderNumber(null);
            Str::simtabiLacommerceTicketNumber('support');
        } finally {
            restore_error_handler();
        }

        $this->assertSame(0, $raised);
    }

    #[Test]
    public function each_bare_alias_delegates_to_its_scoped_macro(): void
    {
        // Replace the scoped implementations: the bare aliases must follow, which proves they forward
        // rather than carry a second copy of the logic.
        foreach (StrMacros::DEPRECATED_ALIASES as $bare => $scoped) {
            Str::macro($scoped, static fn (...$arguments): string => "{$scoped}:" . implode('|', array_map('strval', $arguments)));
        }

        $this->assertSame('simtabiLacommerceSku:laravel|_|P', @Str::sku('laravel', '_', 'P'));
        $this->assertSame('simtabiLacommerceOrderNumber:x|/|INV', @Str::orderNumber('x', '/', 'INV'));
        $this->assertSame('simtabiLacommerceTicketNumber:support', @Str::ticketNumber('support'));
    }

    #[Test]
    public function a_host_overriding_a_bare_name_no_longer_changes_what_the_traits_generate(): void
    {
        Str::macro('sku', static fn (): string => 'HIJACKED');

        $product = DummyProduct::create(['name' => 'Laravel is Awesome']);

        $this->assertMatchesRegularExpression('/^LAR-\d{10}$/', $product->sku);
    }
}
