<?php

declare(strict_types=1);

namespace Simtabi\Lacommerce\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Simtabi\Lacommerce\Tests\Fixtures\Models\DummyProduct;
use Simtabi\Lacommerce\Tests\TestCase;

final class GenerateOnCreateDisabledTest extends TestCase
{
    protected function publishedConfig(): array
    {
        $config = self::defaultConfig();
        $config['generator']['default']['generate_on_create'] = false;
        $config['generator']['default']['refresh_on_update']  = false;

        return ['simtabi.lacommerce' => $config];
    }

    #[Test]
    public function it_does_not_generate_when_disabled(): void
    {
        $product = DummyProduct::create(['name' => 'Blue shirt']);

        $this->assertNull($product->fresh()->getRawOriginal('sku'));

        $product->update(['name' => 'Red hat']);

        $this->assertNull($product->fresh()->getRawOriginal('sku'));
    }
}
