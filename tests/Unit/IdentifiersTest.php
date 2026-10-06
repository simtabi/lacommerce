<?php

declare(strict_types=1);

namespace Simtabi\Lacommerce\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Simtabi\Lacommerce\Supports\Identifiers;

/**
 * The registry-free API. Plain PHPUnit: it needs no application, no config and no macro.
 */
final class IdentifiersTest extends TestCase
{
    #[Test]
    public function sku_takes_the_first_three_studly_characters_of_the_source(): void
    {
        $this->assertMatchesRegularExpression('/^LAR-\d{10}$/', Identifiers::sku('laravel is awesome'));
        $this->assertMatchesRegularExpression('/^LAR_\d{10}$/', Identifiers::sku('laravel', '_'));
    }

    #[Test]
    public function order_number_defaults_to_the_ord_prefix(): void
    {
        $this->assertMatchesRegularExpression('/^ORD-\d{10}$/', Identifiers::orderNumber());
        $this->assertMatchesRegularExpression('/^ORD-\d{10}$/', Identifiers::orderNumber(null));
        $this->assertMatchesRegularExpression('/^ORD-\d{10}$/', Identifiers::orderNumber(''));
    }

    #[Test]
    public function order_number_uses_its_prefix_and_separator(): void
    {
        $this->assertMatchesRegularExpression('/^INV\/\d{10}$/', Identifiers::orderNumber('inv', '/'));
    }

    #[Test]
    public function ticket_number_takes_the_first_three_studly_characters_of_the_source(): void
    {
        $this->assertMatchesRegularExpression('/^SUP-\d{10}$/', Identifiers::ticketNumber('support request'));
        $this->assertMatchesRegularExpression('/^SUP\.\d{10}$/', Identifiers::ticketNumber('support', '.'));
    }

    #[Test]
    public function the_separator_is_used_verbatim(): void
    {
        // The macros resolve an empty separator to the configured default before they get here; the
        // class itself does not second-guess its caller.
        $this->assertMatchesRegularExpression('/^LAR\d{10}$/', Identifiers::sku('laravel', ''));
    }

    #[Test]
    public function the_class_is_final_and_its_constants_name_the_defaults(): void
    {
        $this->assertTrue((new \ReflectionClass(Identifiers::class))->isFinal());
        $this->assertSame('-', Identifiers::DEFAULT_SEPARATOR);
        $this->assertSame('ORD', Identifiers::DEFAULT_ORDER_PREFIX);
    }
}
