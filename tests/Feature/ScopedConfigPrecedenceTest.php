<?php

declare(strict_types=1);

namespace Simtabi\Lacommerce\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Simtabi\Lacommerce\Tests\TestCase;

/**
 * Both files published: the vendor-scoped one wins.
 */
final class ScopedConfigPrecedenceTest extends TestCase
{
    protected function publishedConfig(): array
    {
        $legacy = self::defaultConfig();
        $legacy['generator']['default']['separator'] = '_';

        $scoped = self::defaultConfig();
        $scoped['generator']['default']['separator'] = '/';

        return ['lacommerce' => $legacy, 'simtabi.lacommerce' => $scoped];
    }

    #[Test]
    public function the_vendor_scoped_config_takes_precedence_over_the_bare_one(): void
    {
        $this->assertSame('/', config('simtabi.lacommerce.generator.default.separator'));
        $this->assertSame('/', config('lacommerce.generator.default.separator'));
    }
}
