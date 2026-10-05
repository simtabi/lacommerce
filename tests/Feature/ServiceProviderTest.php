<?php

declare(strict_types=1);

namespace Simtabi\Lacommerce\Tests\Feature;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Simtabi\Lacommerce\Generators\Concerns\OrderNumber\OrderNumberConfigs;
use Simtabi\Lacommerce\Generators\Concerns\Sku\SkuConfigs;
use Simtabi\Lacommerce\Generators\Concerns\TicketNumber\TicketNumberConfigs;
use Simtabi\Lacommerce\Providers\LacommerceServiceProvider;
use Simtabi\Lacommerce\Tests\TestCase;

final class ServiceProviderTest extends TestCase
{
    #[Test]
    public function it_merges_the_defaults_under_the_vendor_scoped_key(): void
    {
        $this->assertSame('-', config('simtabi.lacommerce.generator.default.separator'));
        $this->assertSame('sku', config('simtabi.lacommerce.generator.sku.destination_column'));
        $this->assertSame('order_number', config('simtabi.lacommerce.generator.order_number.destination_column'));
        $this->assertSame('ticket_number', config('simtabi.lacommerce.generator.ticket_number.destination_column'));
    }

    #[Test]
    public function it_mirrors_the_config_to_the_deprecated_bare_key(): void
    {
        $this->assertSame(config('simtabi.lacommerce'), config('lacommerce'));
    }

    #[Test]
    public function it_registers_the_vendor_scoped_publish_tag(): void
    {
        $paths = ServiceProvider::pathsToPublish(LacommerceServiceProvider::class, 'simtabi::lacommerce-config');

        $this->assertCount(1, $paths);
        $this->assertSame(config_path('simtabi/lacommerce.php'), array_values($paths)[0]);
    }

    #[Test]
    public function it_keeps_the_deprecated_bare_publish_tag(): void
    {
        $paths = ServiceProvider::pathsToPublish(LacommerceServiceProvider::class, 'lacommerce:config');

        $this->assertCount(1, $paths);
        $this->assertSame(config_path('lacommerce.php'), array_values($paths)[0]);
    }

    #[Test]
    public function it_registers_no_tag_for_directories_the_package_does_not_ship(): void
    {
        $groups = ServiceProvider::publishableGroups();

        foreach (['assets', 'views', 'translations'] as $suffix) {
            $this->assertNotContains("lacommerce:{$suffix}", $groups);
            $this->assertNotContains("simtabi::lacommerce-{$suffix}", $groups);
        }
    }

    #[Test]
    public function it_does_not_load_migrations_into_the_host_application(): void
    {
        $this->assertSame([], $this->app['migrator']->paths());
    }

    #[Test]
    public function it_binds_the_configs_for_each_generator(): void
    {
        $this->assertSame('sku', $this->app->make(SkuConfigs::class)->getDestinationColumn());
        $this->assertSame('order_number', $this->app->make(OrderNumberConfigs::class)->getDestinationColumn());
        $this->assertSame('ticket_number', $this->app->make(TicketNumberConfigs::class)->getDestinationColumn());
    }

    #[Test]
    public function it_registers_the_str_macros(): void
    {
        $this->assertTrue(Str::hasMacro('sku'));
        $this->assertTrue(Str::hasMacro('orderNumber'));
        $this->assertTrue(Str::hasMacro('ticketNumber'));
    }
}
