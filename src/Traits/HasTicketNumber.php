<?php

namespace Simtabi\Lacommerce\Traits;

use Simtabi\Lacommerce\Generators\Concerns\TicketNumber\TicketNumberConfigs;
use Simtabi\Lacommerce\Generators\Concerns\TicketNumber\TicketNumberObserver;

trait HasTicketNumber
{

    /**
     * Boot the trait by adding observers.
     *
     * @return void
     */
    public static function bootHasTicketNumber()
    {
        static::observe(TicketNumberObserver::class);
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
