<?php

declare(strict_types=1);

namespace Simtabi\Lacommerce\Tests\Feature;

use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Simtabi\Lacommerce\Tests\Fixtures\Generators\MacroSkuGenerator;
use Simtabi\Lacommerce\Tests\Fixtures\Models\DummyProduct;
use Simtabi\Lacommerce\Tests\TestCase;

/**
 * Generator::makeValue() calls the `Str` macro a custom generator names as its `$strMixin`. It passed the
 * source and separator and dropped the configured prefix, which the shipped generators honour.
 */
final class CustomStrMixinPrefixTest extends TestCase
{
    protected function publishedConfig(): array
    {
        $config = self::defaultConfig();
        $config['generator']['sku']['generator'] = MacroSkuGenerator::class;
        $config['generator']['sku']['prefix']    = 'acme';

        return ['simtabi.lacommerce' => $config];
    }

    protected function setUp(): void
    {
        parent::setUp();

        Str::macro(
            MacroSkuGenerator::MACRO,
            static fn (string $source, string $separator, ?string $prefix = null): string => implode('|', [$prefix ?? 'none', $source, $separator]),
        );
    }

    #[Test]
    public function the_configured_prefix_reaches_a_custom_str_mixin(): void
    {
        $this->assertSame('acme|Blue shirt|-', DummyProduct::create(['name' => 'Blue shirt'])->sku);
    }

    #[Test]
    public function a_null_prefix_reaches_it_as_null(): void
    {
        config()->set('simtabi.lacommerce.generator.sku.prefix', null);

        $this->assertSame('none|Blue shirt|-', DummyProduct::create(['name' => 'Blue shirt'])->sku);
    }
}
