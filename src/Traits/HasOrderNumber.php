<?php

namespace Simtabi\Lacommerce\Traits;

use Simtabi\Lacommerce\Generators\Concerns\OrderNumber\OrderNumberConfigs;
use Simtabi\Lacommerce\Generators\Concerns\OrderNumber\OrderNumberObserver;

trait HasOrderNumber
{

    /**
     * Boot the trait by adding observers.
     *
     * @return void
     */
    public static function bootHasOrderNumber()
    {
        static::observe(OrderNumberObserver::class);
    }

    /**
     * Get the Configs for generating the OrderNumber.
     *
     * @return OrderNumberConfigs
     */
    public function orderNumberConfigs(): OrderNumberConfigs
    {
        return resolve(OrderNumberConfigs::class);
    }

    /**
     * Fetch SKU Config.
     *
     * @param  string  $key
     * @return mixed
     */
    public function orderNumberConfig(string $key): mixed
    {
        return $this->orderNumberConfigs()->{$key};
    }

    /**
     * Unless the field is called something else, we can safely get the value from the attribute.
     *
     * @param  mixed  $value
     * @return string
     */
    public function getOrderNumberAttribute($value)
    {
        return (string) $value ?: $this->getAttribute($this->orderNumberConfig('destinationColumn'));
    }

}
