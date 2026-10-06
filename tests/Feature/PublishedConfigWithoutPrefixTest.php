<?php

declare(strict_types=1);

namespace Simtabi\Lacommerce\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Simtabi\Lacommerce\Tests\Fixtures\Models\DummyOrder;
use Simtabi\Lacommerce\Tests\Fixtures\Models\DummyProduct;
use Simtabi\Lacommerce\Tests\TestCase;

/**
 * A config file published before `prefix` existed has no such key in its generator blocks.
 */
final class PublishedConfigWithoutPrefixTest extends TestCase
{
    protected function publishedConfig(): array
    {
        $config = self::defaultConfig();

        foreach (['sku', 'ticket_number', 'order_number'] as $key) {
            unset($config['generator'][$key]['prefix']);
        }

        return ['simtabi.lacommerce' => $config];
    }

    #[Test]
    public function a_missing_prefix_key_means_no_prefix(): void
    {
        $this->assertNull((new DummyProduct())->skuConfigs()->getPrefix());
        $this->assertMatchesRegularExpression('/^BLU-\d{10}$/', DummyProduct::create(['name' => 'Blue shirt'])->sku);
        $this->assertMatchesRegularExpression('/^ORD-\d{10}$/', DummyOrder::create(['name' => 'Blue shirt'])->order_number);
    }
}
