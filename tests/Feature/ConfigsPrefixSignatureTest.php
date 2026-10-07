<?php

declare(strict_types=1);

namespace Simtabi\Lacommerce\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use ReflectionMethod;
use ReflectionNamedType;
use Simtabi\Lacommerce\Generators\Concerns\Sku\SkuConfigs;
use Simtabi\Lacommerce\Generators\Services\Configs;
use Simtabi\Lacommerce\Generators\Services\Contracts\ConfigsInterface;
use Simtabi\Lacommerce\Tests\Fixtures\Models\DummyProduct;
use Simtabi\Lacommerce\Tests\TestCase;

/**
 * ConfigsInterface::setPrefix() took `string` while Configs::setPrefix() took `mixed`, so the contract
 * refused the null the class's own getPrefix() returns. Both take `?string` now.
 */
final class ConfigsPrefixSignatureTest extends TestCase
{
    protected function publishedConfig(): array
    {
        $config = self::defaultConfig();
        $config['generator']['sku']['prefix'] = 42;

        return ['simtabi.lacommerce' => $config];
    }

    #[Test]
    public function the_interface_and_the_class_declare_the_same_nullable_string_parameter(): void
    {
        foreach ([ConfigsInterface::class, Configs::class] as $class) {
            $type = (new ReflectionMethod($class, 'setPrefix'))->getParameters()[0]->getType();

            $this->assertInstanceOf(ReflectionNamedType::class, $type, "{$class}::setPrefix()");
            $this->assertSame('string', $type->getName(), "{$class}::setPrefix()");
            $this->assertTrue($type->allowsNull(), "{$class}::setPrefix()");
        }
    }

    #[Test]
    public function the_prefix_can_be_set_and_cleared_through_the_interface(): void
    {
        $configs = SkuConfigs::make();

        $this->assertSame('ACME', $configs->setPrefix('ACME')->getPrefix());
        $this->assertNull($configs->setPrefix(null)->getPrefix());
    }

    #[Test]
    public function a_numeric_prefix_in_the_config_file_still_works(): void
    {
        $this->assertSame('42', SkuConfigs::make()->getPrefix());
        $this->assertMatchesRegularExpression('/^42-BLU-\d{10}$/', DummyProduct::create(['name' => 'Blue shirt'])->sku);
    }
}
