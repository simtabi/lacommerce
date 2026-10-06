<?php

namespace Simtabi\Lacommerce\Providers;

use Illuminate\Contracts\Foundation\CachesConfiguration;
use Illuminate\Support\ServiceProvider;
use Simtabi\Lacommerce\Generators\Concerns\OrderNumber\OrderNumberConfigs;
use Simtabi\Lacommerce\Generators\Concerns\Sku\SkuConfigs;
use Simtabi\Lacommerce\Generators\Concerns\TicketNumber\TicketNumberConfigs;
use Simtabi\Lacommerce\Generators\Contracts\SkuGeneratorInterface;
use Simtabi\Lacommerce\Generators\Contracts\TicketNumberGeneratorInterface;
use Simtabi\Lacommerce\Generators\Contracts\OrderNumberGeneratorInterface;
use Simtabi\Lacommerce\Supports\StrMacros;

class LacommerceServiceProvider extends ServiceProvider
{

    /**
     * Vendor-scoped config key. Published to config/simtabi/lacommerce.php.
     */
    public const CONFIG_KEY = 'simtabi.lacommerce';

    /**
     * Vendor-scoped publish tag for the config file.
     */
    public const CONFIG_TAG = 'simtabi::lacommerce-config';

    /**
     * Vendor-scoped view and translation namespace.
     */
    public const VIEW_NAMESPACE = 'simtabi/lacommerce';

    /**
     * Bare config key used before 0.1.0.
     *
     * @deprecated Since 0.1.0; use CONFIG_KEY (`simtabi.lacommerce`). Still populated and a
     *             published config/lacommerce.php is still honoured. Earliest removal: 0.2.0.
     */
    public const LEGACY_CONFIG_KEY = 'lacommerce';

    /**
     * Bare publish tag used before 0.1.0.
     *
     * @deprecated Since 0.1.0; use CONFIG_TAG (`simtabi::lacommerce-config`). Still registered and
     *             still publishes config/lacommerce.php. Earliest removal: 0.2.0.
     */
    public const LEGACY_CONFIG_TAG = 'lacommerce:config';

    private const  PACKAGE_PATH = __DIR__ . '/../../';

    /**
     * Register application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConfig();

        if (is_dir($path = self::PACKAGE_PATH . 'resources/lang')) {
            $this->loadTranslationsFrom($path, self::VIEW_NAMESPACE);
        }

        if (is_dir($path = self::PACKAGE_PATH . 'resources/views')) {
            $this->loadViewsFrom($path, self::VIEW_NAMESPACE);
        }
    }

    /**
     * Merge the package defaults into the vendor-scoped key, honouring a config file published
     * under the deprecated bare key, and mirror the result to that bare key so existing
     * `config('lacommerce.*')` reads keep working.
     *
     * Precedence, lowest to highest: package defaults, config/lacommerce.php (deprecated),
     * config/simtabi/lacommerce.php. The merge is shallow, as mergeConfigFrom() is.
     */
    private function registerConfig(): void
    {
        if ($this->app instanceof CachesConfiguration && $this->app->configurationIsCached()) {
            return;
        }

        $config = $this->app->make('config');

        $merged = array_merge(
            require self::PACKAGE_PATH . 'config/config.php',
            (array) $config->get(self::LEGACY_CONFIG_KEY, []),
            (array) $config->get(self::CONFIG_KEY, []),
        );

        $config->set(self::CONFIG_KEY, $merged);
        $config->set(self::LEGACY_CONFIG_KEY, $merged);
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
            // Laravel keys a provider's publishable paths by source path, so two tags naming the
            // same source string would share one target and the later one would win. The two
            // spellings below name the same file but are distinct keys.
            $this->publishes([
                dirname(__DIR__, 2) . '/config/config.php' => config_path('simtabi/lacommerce.php'),
            ], self::CONFIG_TAG);

            // Deprecated bare tag, kept so existing install instructions still work.
            $this->publishes([
                self::PACKAGE_PATH . 'config/config.php' => config_path(self::LEGACY_CONFIG_KEY . '.php'),
            ], self::LEGACY_CONFIG_TAG);

            // The package ships no public assets, views or translations today. The tags are
            // registered only when the directory exists, so they never point at nothing.
            $optional = [
                'public'          => [public_path('vendor/simtabi/lacommerce'), 'simtabi::lacommerce-assets'],
                'resources/views' => [resource_path('views/vendor/simtabi/lacommerce'), 'simtabi::lacommerce-views'],
                'resources/lang'  => [$this->app->langPath('vendor/simtabi/lacommerce'), 'simtabi::lacommerce-translations'],
            ];

            foreach ($optional as $source => [$target, $tag]) {
                if (is_dir(self::PACKAGE_PATH . $source)) {
                    $this->publishes([self::PACKAGE_PATH . $source => $target], $tag);
                }
            }
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

        return $config->get(self::CONFIG_KEY . '.generator', []);
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

    /**
     * Register the vendor-scoped Str macros, and the deprecated bare ones unless the host opted out.
     *
     * The bodies moved to Supports\StrMacros, which forwards to Supports\Identifiers.
     */
    private function registerStrMacros(): void
    {
        StrMacros::register(
            (string) config(self::CONFIG_KEY . '.generator.default.separator', '-'),
            (bool) config(self::CONFIG_KEY . '.register_legacy_macros', true),
        );
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
