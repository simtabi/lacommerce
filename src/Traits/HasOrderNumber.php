<?php

namespace Simtabi\Lacommerce\Traits;

use Illuminate\Database\Eloquent\Model;
use Simtabi\Lacommerce\Generators\Concerns\OrderNumber\OrderNumberConfigs;
use Simtabi\Lacommerce\Generators\Concerns\OrderNumber\OrderNumberObserver;

trait HasOrderNumber
{

    /**
     * Boot the trait by registering the observer's model events.
     *
     * The listeners are registered directly rather than through static::observe(), which
     * instantiates the model and therefore throws while the model is still booting.
     *
     * @return void
     */
    public static function bootHasOrderNumber()
    {
        static::creating(static fn (Model $model) => resolve(OrderNumberObserver::class)->creating($model));
        static::updating(static fn (Model $model) => resolve(OrderNumberObserver::class)->updating($model));
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
     * Fetch OrderNumber Config.
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
