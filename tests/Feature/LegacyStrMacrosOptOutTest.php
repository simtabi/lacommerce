<?php

declare(strict_types=1);

namespace Simtabi\Lacommerce\Tests\Feature;

use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Simtabi\Lacommerce\Tests\Fixtures\Models\DummyOrder;
use Simtabi\Lacommerce\Tests\Fixtures\Models\DummyProduct;
use Simtabi\Lacommerce\Tests\Fixtures\Models\DummyTicket;
use Simtabi\Lacommerce\Tests\TestCase;

/**
 * `register_legacy_macros => false`: the bare names are left free for the host.
 */
final class LegacyStrMacrosOptOutTest extends TestCase
{
    protected function setUp(): void
    {
        Str::flushMacros();

        parent::setUp();
    }

    protected function publishedConfig(): array
    {
        $scoped = self::defaultConfig();
        $scoped['register_legacy_macros'] = false;

        return ['simtabi.lacommerce' => $scoped];
    }

    #[Test]
    public function the_bare_macros_are_not_registered(): void
    {
        $this->assertFalse(Str::hasMacro('sku'));
        $this->assertFalse(Str::hasMacro('orderNumber'));
        $this->assertFalse(Str::hasMacro('ticketNumber'));
    }

    #[Test]
    public function the_scoped_macros_are_still_registered(): void
    {
        $this->assertTrue(Str::hasMacro('simtabiLacommerceSku'));
        $this->assertTrue(Str::hasMacro('simtabiLacommerceOrderNumber'));
        $this->assertTrue(Str::hasMacro('simtabiLacommerceTicketNumber'));
    }

    #[Test]
    public function the_traits_still_generate_values_without_the_bare_macros(): void
    {
        $this->assertMatchesRegularExpression('/^LAR-\d{10}$/', DummyProduct::create(['name' => 'Laravel'])->sku);
        $this->assertMatchesRegularExpression('/^ORD-\d{10}$/', DummyOrder::create(['name' => 'Blue shirt'])->order_number);
        $this->assertMatchesRegularExpression('/^SUP-\d{10}$/', DummyTicket::create(['name' => 'Support'])->ticket_number);
    }
}
