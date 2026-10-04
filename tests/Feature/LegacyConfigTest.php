<?php

declare(strict_types=1);

namespace Simtabi\Lacommerce\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Simtabi\Lacommerce\Tests\Fixtures\Models\DummyProduct;
use Simtabi\Lacommerce\Tests\TestCase;

/**
 * An application that published config/lacommerce.php before the key was vendor-scoped.
 */
final class LegacyConfigTest extends TestCase
{
    protected function publishedConfig(): array
    {
        $legacy = self::defaultConfig();
        $legacy['generator']['default']['separator'] = '_';

        return ['lacommerce' => $legacy];
    }

    #[Test]
    public function it_honours_a_config_published_under_the_bare_key(): void
    {
        $this->assertSame('_', config('simtabi.lacommerce.generator.default.separator'));
        $this->assertSame('_', config('lacommerce.generator.default.separator'));

        $product = DummyProduct::create(['name' => 'Blue shirt']);

        $this->assertMatchesRegularExpression('/^BLU_\d{10}$/', $product->sku);
    }
}
