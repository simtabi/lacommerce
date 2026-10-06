<?php

declare(strict_types=1);

namespace Simtabi\Lacommerce\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Simtabi\Lacommerce\Generators\Concerns\OrderNumber\OrderNumberGenerator;
use Simtabi\Lacommerce\Generators\Concerns\Sku\SkuGenerator;
use Simtabi\Lacommerce\Generators\Concerns\TicketNumber\TicketNumberGenerator;
use Simtabi\Lacommerce\Generators\Contracts\OrderNumberGeneratorInterface;
use Simtabi\Lacommerce\Generators\Contracts\SkuGeneratorInterface;
use Simtabi\Lacommerce\Generators\Contracts\TicketNumberGeneratorInterface;
use Simtabi\Lacommerce\Generators\Services\Generator;
use Simtabi\Lacommerce\Tests\Fixtures\Models\DummyOrder;
use Simtabi\Lacommerce\Tests\Fixtures\Models\DummyProduct;
use Simtabi\Lacommerce\Tests\Fixtures\Models\DummyTicket;
use Simtabi\Lacommerce\Tests\TestCase;

final class ShippedGeneratorsTest extends TestCase
{
    /**
     * The shipped generators stay final; the documented seam is the non-final base class and the
     * interfaces. If either of these flips, docs/tools/generators.md has to change with it.
     */
    #[Test]
    public function the_shipped_generators_are_final_and_the_base_is_not(): void
    {
        foreach ([SkuGenerator::class, OrderNumberGenerator::class, TicketNumberGenerator::class] as $class) {
            $this->assertTrue((new \ReflectionClass($class))->isFinal(), "{$class} is not final");
        }

        $base = new \ReflectionClass(Generator::class);
        $this->assertFalse($base->isFinal());

        foreach (['getSourceString', 'makeValue', 'exists', 'generate'] as $hook) {
            $this->assertTrue($base->getMethod($hook)->isProtected(), "Generator::{$hook}() is not a protected hook");
        }
    }

    #[Test]
    public function each_shipped_generator_implements_the_interface_it_is_resolved_through(): void
    {
        $this->assertInstanceOf(SkuGeneratorInterface::class, resolve(SkuGeneratorInterface::class, ['model' => new DummyProduct()]));
        $this->assertInstanceOf(OrderNumberGeneratorInterface::class, resolve(OrderNumberGeneratorInterface::class, ['model' => new DummyOrder()]));
        $this->assertInstanceOf(TicketNumberGeneratorInterface::class, resolve(TicketNumberGeneratorInterface::class, ['model' => new DummyTicket()]));
    }

    #[Test]
    public function the_prefix_defaults_to_none(): void
    {
        // Configs::$prefix was declared without a default, so getPrefix() threw before setPrefix().
        $this->assertNull((new DummyProduct())->skuConfigs()->getPrefix());
        $this->assertNull((new DummyProduct())->skuConfig('prefix'));
    }
}
