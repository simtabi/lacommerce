<?php

declare(strict_types=1);

namespace Simtabi\Lacommerce\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Simtabi\Lacommerce\Generators\Concerns\Sku\SkuConfigs;
use Simtabi\Lacommerce\Generators\Contracts\SkuGeneratorInterface;
use Simtabi\Lacommerce\Providers\LacommerceServiceProvider;
use Simtabi\Lacommerce\Tests\Fixtures\Generators\FallbackSkuGenerator;
use Simtabi\Lacommerce\Tests\Fixtures\Models\DummyProduct;
use Simtabi\Lacommerce\Tests\TestCase;

/**
 * The provider used to read the generator config once, at boot, and close over it, so a later config()
 * change never reached the bindings. They read it each time they resolve now.
 */
final class RuntimeConfigTest extends TestCase
{
    private const GENERATOR = LacommerceServiceProvider::CONFIG_KEY . '.generator';

    #[Test]
    public function a_config_change_after_boot_reaches_the_configs_binding(): void
    {
        config()->set(self::GENERATOR . '.sku.prefix', 'live');
        config()->set(self::GENERATOR . '.default.separator', '_');

        $configs = SkuConfigs::make();

        $this->assertSame('live', $configs->getPrefix());
        $this->assertSame('_', $configs->getSeparator());
        $this->assertMatchesRegularExpression('/^LIVE_BLU_\d{10}$/', DummyProduct::create(['name' => 'Blue shirt'])->sku);
    }

    #[Test]
    public function a_generator_class_configured_after_boot_is_the_one_resolved(): void
    {
        config()->set(self::GENERATOR . '.sku.generator', FallbackSkuGenerator::class);

        $this->assertInstanceOf(
            FallbackSkuGenerator::class,
            resolve(SkuGeneratorInterface::class, ['model' => new DummyProduct(['name' => 'Blue shirt'])]),
        );
        $this->assertMatchesRegularExpression('/^ITE-\d{10}$/', DummyProduct::create(['name' => ''])->sku);
    }
}
