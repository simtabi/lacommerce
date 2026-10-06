<?php

declare(strict_types=1);

namespace Simtabi\Lacommerce\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Simtabi\Lacommerce\Generators\Exceptions\InvalidOptionException;
use Simtabi\Lacommerce\Tests\Fixtures\Models\DummyProduct;
use Simtabi\Lacommerce\Tests\TestCase;

final class InvalidGeneratorConfigTest extends TestCase
{
    protected function publishedConfig(): array
    {
        $config = self::defaultConfig();
        $config['generator']['sku']['generator'] = \stdClass::class;

        return ['simtabi.lacommerce' => $config];
    }

    #[Test]
    public function a_class_that_is_not_a_generator_is_rejected_with_the_config_key_named(): void
    {
        $this->expectException(InvalidOptionException::class);
        $this->expectExceptionMessage('simtabi.lacommerce.generator.sku.generator');

        DummyProduct::create(['name' => 'Blue shirt']);
    }
}
