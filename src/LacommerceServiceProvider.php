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

class LacommerceServiceProvider extends ServiceProvider
{

    public const PATH = __DIR__ . '/../';

    /**
     * Register application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(self::PATH . 'config/lacommerce.php', 'lacommerce');
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

    private function registerConsoles()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                self::PATH . 'config/lacommerce.php'   => config_path('lacommerce.php'),
            ], 'lacommerce:config');

            $this->publishes([
                self::PATH . 'resources/assets/media'  => public_path('vendor/lacommerce'),
            ], 'lacommerce:assets');

            $this->publishes([
                self::PATH . 'resources/views'         => resource_path('views/vendor/lacommerce'),
            ], 'lacommerce:views');
        }
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

            // signature
            $signature = str_shuffle(str_repeat(str_pad('0123456789', 10, rand(0, 9).rand(0, 9), STR_PAD_LEFT), 2));
            // Sanitize the signature
            $signature = substr($signature, 0, 10);
            // Implode with random
            $result    = !empty($prefix) ? implode($separator, [$prefix, $source, $signature]) : implode($separator, [$source, $signature]);
            // Uppercase it
            return Str::upper($result);
        });

        Str::macro('orderNumber', function (string $source, ?string $separator = null, ?string $prefix = null) use ($defSeparator){
            $separator = $separator ?: $defSeparator;
            if (!empty($source))
            {
                // Clean up the source
                $source = Str::studly($source);
                // Limit the source
                $source = Str::limit($source, 3, '');
            }else{
                $source = 'ORD';
            }

            // signature
            $signature = str_shuffle(str_repeat(str_pad('0123456789', 10, rand(0, 9).rand(0, 9), STR_PAD_LEFT), 2));
            // Sanitize the signature
            $signature = substr($signature, 0, 10);
            // Implode with random
            $result    = !empty($prefix) ? implode($separator, [$prefix, $source, $signature]) : implode($separator, [$source, $signature]);
            // Uppercase it
            return Str::upper($result);
        });

        Str::macro('ticketNumber', function (string $source, ?string $separator = null, ?string $prefix = null) use ($defSeparator){
            $separator = $separator ?: $defSeparator;
            // Clean up the source
            $source    = Str::studly($source);
            // Limit the source
            $source    = Str::limit($source, 3, '');
            // signature
            $signature = str_shuffle(str_repeat(str_pad('0123456789', 10, rand(0, 9).rand(0, 9), STR_PAD_LEFT), 2));
            // Sanitize the signature
            $signature = substr($signature, 0, 10);
            // Implode with random
            $result    = !empty($prefix) ? implode($separator, [$prefix, $source, $signature]) : implode($separator, [$source, $signature]);
            // Uppercase it
            return Str::upper($result);
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
