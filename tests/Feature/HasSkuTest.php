<?php

declare(strict_types=1);

namespace Simtabi\Lacommerce\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Simtabi\Lacommerce\Generators\Concerns\Sku\SkuConfigs;
use Simtabi\Lacommerce\Tests\Fixtures\Models\DummyProduct;
use Simtabi\Lacommerce\Tests\TestCase;

final class HasSkuTest extends TestCase
{
    #[Test]
    public function it_generates_a_sku_on_create(): void
    {
        $product = DummyProduct::create(['name' => 'Blue shirt']);

        $this->assertMatchesRegularExpression('/^BLU-\d{10}$/', $product->sku);
        $this->assertSame($product->sku, $product->fresh()->sku);
    }

    #[Test]
    public function it_refreshes_the_sku_when_the_source_column_changes(): void
    {
        $product = DummyProduct::create(['name' => 'Blue shirt']);

        $product->update(['name' => 'Red hat']);

        $this->assertMatchesRegularExpression('/^RED-\d{10}$/', $product->sku);
    }

    #[Test]
    public function it_keeps_a_sku_that_was_set_by_hand_on_update(): void
    {
        $product = DummyProduct::create(['name' => 'Blue shirt']);

        $product->update(['name' => 'Red hat', 'sku' => 'MANUAL-1']);

        $this->assertSame('MANUAL-1', $product->fresh()->sku);
    }

    #[Test]
    public function it_leaves_the_sku_alone_when_an_unrelated_column_changes(): void
    {
        $product = DummyProduct::create(['name' => 'Blue shirt']);
        $sku     = $product->sku;

        $product->touch();

        $this->assertSame($sku, $product->fresh()->sku);
    }

    #[Test]
    public function it_exposes_its_configs(): void
    {
        $product = new DummyProduct();

        $this->assertInstanceOf(SkuConfigs::class, $product->skuConfigs());
        $this->assertSame('sku', $product->skuConfig('destinationColumn'));
        $this->assertSame(['name'], $product->skuConfig('sourceColumn'));
    }
}
