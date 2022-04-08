<?php

namespace Simtabi\Lacommerce;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Simtabi\Lacommerce\Generators\Concerns\OrderNumber\OrderNumberConfigs;
use Simtabi\Lacommerce\Generators\Concerns\Sku\SkuConfigs;
use Simtabi\Lacommerce\Generators\Concerns\TicketNumber\TicketNumberConfigs;
use Simtabi\Lacommerce\Generators\Contracts\SkuGeneratorInterface;
use Simtabi\Lacommerce\Generators\Contracts\TicketNumberGeneratorInterface;
use Simtabi\Lacommerce\Generators\Contracts\OrderNumberGeneratorInterface;
use Simtabi\Lacommerce\Supports\Helpers;

class LacommerceServiceProvider extends ServiceProvider
{

    private string $packageName = 'lacommerce';
    private const  PACKAGE_PATH = __DIR__ . '/../';

    /**
     * Register application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(self::PACKAGE_PATH . 'config/config.php', $this->packageName);
        $this->loadTranslationsFrom(self::PACKAGE_PATH . "resources/lang/", $this->packageName);
        $this->loadMigrationsFrom(self::PACKAGE_PATH.'/../database/migrations');
        $this->loadViewsFrom(self::PACKAGE_PATH . "resources/views", $this->packageName);
        $this->mergeConfigFrom(self::PACKAGE_PATH . "config/config.php", $this->packageName);
    }

    /**
     * Boot application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerConsoles();

        // Bind the Generator
        $this->bindGenerator();

        // Bind Standard Configs
        $this->bindConfigs();

        // Extend Str with specified methods
        $this->registerStrMacros();
    }

    private function registerConsoles(): static
    {
        if ($this->app->runningInConsole())
        {
            $this->publishes([
                self::PACKAGE_PATH . "config/config.php"               => config_path("{$this->packageName}.php"),
            ], "{$this->packageName}:config");

            $this->publishes([
                self::PACKAGE_PATH . "public"                          => public_path("vendor/{$this->packageName}"),
            ], "{$this->packageName}:assets");

            $this->publishes([
                self::PACKAGE_PATH . "resources/views"                 => resource_path("views/vendor/{$this->packageName}"),
            ], "{$this->packageName}:views");

            $this->publishes([
                self::PACKAGE_PATH . "resources/lang"                  => $this->app->langPath("vendor/{$this->packageName}"),
            ], "{$this->packageName}:translations");
        }

        return $this;
    }

    /**
     * Bind the Generator.
     *
     * @return void
     */
    protected function bindGenerator()
    {

        $config = $this->getConfig();

        $this->app->bind(SkuGeneratorInterface::class, function ($app, array $parameters) use ($config) {
            $generator = $config['sku']['generator'];

            return new $generator(head($parameters));
        });

        $this->app->bind(OrderNumberGeneratorInterface::class, function ($app, array $parameters) use ($config) {
            $generator = $config['order_number']['generator'];

            return new $generator(head($parameters));
        });

        $this->app->bind(TicketNumberGeneratorInterface::class, function ($app, array $parameters) use ($config) {
            $generator = $config['ticket_number']['generator'];

            return new $generator(head($parameters));
        });
    }

    private function getConfig(): array
    {
        $config = $this->app->make('config');

        return $config->get('lacommerce.generator', []);
    }

    /**
     * Bind Configs.
     *
     * @return void
     */
    protected function bindConfigs()
    {

        $config = $this->getConfig();

        $this->app->bind(SkuConfigs::class, function ($app) use ($config) {
            return new SkuConfigs($config, 'sku');
        });

        $this->app->bind(OrderNumberConfigs::class, function ($app) use ($config) {
            return new OrderNumberConfigs($config, 'order_number');
        });

        $this->app->bind(TicketNumberConfigs::class, function ($app) use ($config) {
            return new TicketNumberConfigs($config, 'ticket_number');
        });

    }

    private function registerStrMacros()
    {

        $defSeparator = config('lacommerce.generator.default.separator', '-');

        Str::macro('sku', function (string $source, ?string $separator = null, ?string $prefix = null) use ($defSeparator){
            $separator = $separator ?: $defSeparator;
            // Clean up the source
            $source    = Str::studly($source);
            // Limit the source
            $source    = Str::limit($source, 3, '');
            // Add prefix @todo

            return Helpers::makeRandomString($source, $separator);
        });

        Str::macro('orderNumber', function (?string $source, ?string $separator = null, ?string $prefix = null) use ($defSeparator){
            $separator = $separator ?: $defSeparator;
            $source    = !empty($prefix) ? $prefix: 'ORD';

            return Helpers::makeRandomString($source, $separator);
        });

        Str::macro('ticketNumber', function (string $source, ?string $separator = null, ?string $prefix = null) use ($defSeparator){
            $separator = $separator ?: $defSeparator;
            // Clean up the source
            $source    = Str::studly($source);
            // Limit the source
            $source    = Str::limit($source, 3, '');

            return Helpers::makeRandomString($source, $separator);
        });

    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            SkuGeneratorInterface::class,
            SkuConfigs::class,
            TicketNumberGeneratorInterface::class,
            TicketNumberConfigs::class,
            OrderNumberGeneratorInterface::class,
            OrderNumberConfigs::class,
        ];
    }

}
