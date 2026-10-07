<?php

declare(strict_types=1);

namespace Simtabi\Lacommerce\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Simtabi\Lacommerce\Generators\Concerns\Sku\SkuConfigs;
use Simtabi\Lacommerce\Generators\Exceptions\InvalidOptionException;
use Simtabi\Lacommerce\Generators\Services\Configs;
use Simtabi\Lacommerce\Providers\LacommerceServiceProvider;
use Simtabi\Lacommerce\Tests\Fixtures\Configs\WarehouseSkuConfigs;
use Simtabi\Lacommerce\Tests\TestCase;

/**
 * Configs::make() on the base class used to resolve Configs itself, which the container cannot build: its
 * constructor needs a config array and a key. It now says so, and a subclass that inherits make() gets itself.
 */
final class ConfigsMakeTest extends TestCase
{
    #[Test]
    public function make_on_the_base_class_throws_an_exception_that_names_the_fix(): void
    {
        $this->expectException(InvalidOptionException::class);
        $this->expectExceptionMessage('SkuConfigs::make()');

        Configs::make();
    }

    #[Test]
    public function make_on_a_shipped_subclass_still_returns_that_subclass(): void
    {
        $this->assertInstanceOf(SkuConfigs::class, SkuConfigs::make());
    }

    #[Test]
    public function a_subclass_that_inherits_make_resolves_itself(): void
    {
        $this->app->bind(WarehouseSkuConfigs::class, static fn (): WarehouseSkuConfigs => new WarehouseSkuConfigs(
            (array) config(LacommerceServiceProvider::CONFIG_KEY . '.generator'),
            'sku',
        ));

        $this->assertInstanceOf(WarehouseSkuConfigs::class, WarehouseSkuConfigs::make());
    }
}
