<?php

declare(strict_types=1);

namespace Simtabi\Lacommerce\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Simtabi\Lacommerce\Generators\Concerns\Sku\SkuConfigs;
use Simtabi\Lacommerce\Tests\Fixtures\Models\DummyOrder;
use Simtabi\Lacommerce\Tests\Fixtures\Models\DummyProduct;
use Simtabi\Lacommerce\Tests\Fixtures\Models\DummyTicket;
use Simtabi\Lacommerce\Tests\TestCase;

final class GeneratorPrefixTest extends TestCase
{
    protected function publishedConfig(): array
    {
        $config = self::defaultConfig();
        $config['generator']['sku']['prefix']           = 'acme';
        $config['generator']['ticket_number']['prefix'] = 'HD';
        $config['generator']['order_number']['prefix']  = 'INV';

        return ['simtabi.lacommerce' => $config];
    }

    #[Test]
    public function the_configured_prefix_leads_each_generated_value(): void
    {
        $this->assertMatchesRegularExpression('/^ACME-BLU-\d{10}$/', DummyProduct::create(['name' => 'Blue shirt'])->sku);
        $this->assertMatchesRegularExpression('/^HD-PRI-\d{10}$/', DummyTicket::create(['name' => 'Printer jam'])->ticket_number);
        $this->assertMatchesRegularExpression('/^INV-\d{10}$/', DummyOrder::create(['name' => 'Blue shirt'])->order_number);
    }

    #[Test]
    public function the_configs_expose_the_prefix(): void
    {
        $this->assertSame('acme', (new DummyProduct())->skuConfigs()->getPrefix());
        $this->assertSame('acme', (new DummyProduct())->skuConfig('prefix'));
        $this->assertSame('x', SkuConfigs::make()->setPrefix('x')->getPrefix());
    }
}
