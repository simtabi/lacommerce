<?php

namespace Simtabi\Lacommerce\Traits;

use Simtabi\Lacommerce\Generators\Concerns\Sku\SkuConfigs;
use Simtabi\Lacommerce\Generators\Concerns\Sku\SkuObserver;

trait HasSku
{

    /**
     * Boot the trait by adding observers.
     *
     * @return void
     */
    public static function bootHasSku()
    {
        static::observe(SkuObserver::class);
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
