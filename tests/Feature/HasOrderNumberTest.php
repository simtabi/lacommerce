<?php

declare(strict_types=1);

namespace Simtabi\Lacommerce\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Simtabi\Lacommerce\Generators\Concerns\OrderNumber\OrderNumberConfigs;
use Simtabi\Lacommerce\Tests\Fixtures\Models\DummyOrder;
use Simtabi\Lacommerce\Tests\TestCase;

final class HasOrderNumberTest extends TestCase
{
    #[Test]
    public function it_generates_an_order_number_on_create(): void
    {
        $order = DummyOrder::create(['name' => 'Blue shirt']);

        $this->assertMatchesRegularExpression('/^ORD-\d{10}$/', $order->order_number);
        $this->assertNull($order->sku);
        $this->assertNull($order->ticket_number);
    }

    #[Test]
    public function it_keeps_an_order_number_that_was_set_by_hand_on_update(): void
    {
        $order = DummyOrder::create(['name' => 'Blue shirt']);

        $order->update(['name' => 'Red hat', 'order_number' => 'ORD-MANUAL']);

        $this->assertSame('ORD-MANUAL', $order->fresh()->order_number);
    }

    #[Test]
    public function it_exposes_its_configs(): void
    {
        $order = new DummyOrder();

        $this->assertInstanceOf(OrderNumberConfigs::class, $order->orderNumberConfigs());
        $this->assertSame('order_number', $order->orderNumberConfig('destinationColumn'));
    }
}
