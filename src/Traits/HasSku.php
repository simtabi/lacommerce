<?php

namespace Simtabi\Lacommerce\Traits;

use Illuminate\Database\Eloquent\Model;
use Simtabi\Lacommerce\Generators\Concerns\Sku\SkuConfigs;
use Simtabi\Lacommerce\Generators\Concerns\Sku\SkuObserver;

trait HasSku
{

    /**
     * Boot the trait by registering the observer's model events.
     *
     * The listeners are registered directly rather than through static::observe(), which
     * instantiates the model and therefore throws while the model is still booting.
     *
     * @return void
     */
    public static function bootHasSku()
    {
        static::creating(static fn (Model $model) => resolve(SkuObserver::class)->creating($model));
        static::updating(static fn (Model $model) => resolve(SkuObserver::class)->updating($model));
    }

    /**
     * Get the Configs for generating the Sku.
     *
     * @return SkuConfigs
     */
    public function skuConfigs(): SkuConfigs
    {
        return resolve(SkuConfigs::class);
    }

    /**
     * Fetch SKU Config.
     *
     * @param  string  $key
     * @return mixed
     */
    public function skuConfig(string $key): mixed
    {
        return $this->skuConfigs()->{$key};
    }

    /**
     * Unless the field is called something else, we can safely get the value from the attribute.
     *
     * @param  mixed  $value
     * @return string
     */
    public function getSkuAttribute($value)
    {
        return (string) $value ?: $this->getAttribute($this->skuConfig('destinationColumn'));
    }

}
