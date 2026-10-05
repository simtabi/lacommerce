<?php

namespace Simtabi\Lacommerce\Traits;

use Illuminate\Database\Eloquent\Model;
use Simtabi\Lacommerce\Generators\Concerns\TicketNumber\TicketNumberConfigs;
use Simtabi\Lacommerce\Generators\Concerns\TicketNumber\TicketNumberObserver;

trait HasTicketNumber
{

    /**
     * Boot the trait by registering the observer's model events.
     *
     * The listeners are registered directly rather than through static::observe(), which
     * instantiates the model and therefore throws while the model is still booting.
     *
     * @return void
     */
    public static function bootHasTicketNumber()
    {
        static::creating(static fn (Model $model) => resolve(TicketNumberObserver::class)->creating($model));
        static::updating(static fn (Model $model) => resolve(TicketNumberObserver::class)->updating($model));
    }

    /**
     * Get the Configs for generating the TicketNumber.
     *
     * @return TicketNumberConfigs
     */
    public function ticketNumberConfigs(): TicketNumberConfigs
    {
        return resolve(TicketNumberConfigs::class);
    }

    /**
     * Fetch TicketNumber Config.
     *
     * @param  string  $key
     * @return mixed
     */
    public function ticketNumberConfig(string $key): mixed
    {
        return $this->ticketNumberConfigs()->{$key};
    }

    /**
     * Unless the field is called something else, we can safely get the value from the attribute.
     *
     * @param  mixed  $value
     * @return string
     */
    public function getTicketNumberAttribute($value)
    {
        return (string) $value ?: $this->getAttribute($this->ticketNumberConfig('destinationColumn'));
    }

}
