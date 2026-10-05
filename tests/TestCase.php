<?php

declare(strict_types=1);

namespace Simtabi\Lacommerce\Tests;

use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;
use Simtabi\Lacommerce\Providers\LacommerceServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [LacommerceServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');
    }

    /**
     * Config an application has published, applied where Laravel applies config files: after
     * they load and before any provider registers. defineEnvironment() runs too late for that.
     *
     * @return array<string, mixed>
     */
    protected function publishedConfig(): array
    {
        return [];
    }

    protected function resolveApplicationConfiguration($app)
    {
        parent::resolveApplicationConfiguration($app);

        foreach ($this->publishedConfig() as $key => $value) {
            $app['config']->set($key, $value);
        }
    }

    /**
     * The package's default config file, for building a published copy.
     *
     * @return array<string, mixed>
     */
    protected static function defaultConfig(): array
    {
        return require __DIR__ . '/../config/config.php';
    }

    protected function defineDatabaseMigrations(): void
    {
        require_once __DIR__ . '/Fixtures/database/migrations/CreateDummyModelsTable.php';

        $migration = new \CreateDummyModelsTable();
        $migration->up();

        $this->beforeApplicationDestroyed(static function () use ($migration): void {
            if (Schema::hasTable('dummy_models')) {
                $migration->down();
            }
        });
    }
}
