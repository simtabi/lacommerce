<?php

use Simtabi\Lacommerce\Generators\Services\Contracts\GeneratorInterface;
use Simtabi\Lacommerce\Generators\Concerns\Sku\SkuGenerator;
use Simtabi\Lacommerce\Generators\Concerns\TicketNumber\TicketNumberGenerator;
use Simtabi\Lacommerce\Generators\Concerns\OrderNumber\OrderNumberGenerator;

return [

    /*
    |--------------------------------------------------------------------------
    | Deprecated bare Str macros
    |--------------------------------------------------------------------------
    |
    | 0.1.0 registered Str::sku(), Str::orderNumber() and Str::ticketNumber().
    | Those names sit in Str's flat macro registry, where another package or
    | your application can silently replace them. They still work and are
    | deprecated: use Simtabi\Lacommerce\Supports\Identifiers, or the scoped
    | Str::simtabiLacommerceSku() family. Set this to false to stop registering
    | the bare names and leave them free. Earliest removal: 0.2.0.
    |
    */

    'register_legacy_macros' => true,

    /*
    |--------------------------------------------------------------------------
    | Generator settings
    |--------------------------------------------------------------------------
    |
    */

    'generator' => [
        'default' => [

            /** Separator */
            'separator'          => '-',

            /** Enforce generated values to be unique */
            'unique'             => true,

            /** Generate on create */
            'generate_on_create' => true,

            /** Refresh on update */
            'refresh_on_update'  => true,

        ],

        /*
        |--------------------------------------------------------------------------
        | SKU Generator
        |--------------------------------------------------------------------------
        |
        */
        'sku'           => [
            /** Generator and must @implements GeneratorInterface */
            'generator'          => SkuGenerator::class,

            /** Source field(column) */
            'source_column'      => 'name',

            /** Destination field(column) */
            'destination_column' => 'sku',
        ],

        /*
        |--------------------------------------------------------------------------
        | TicketNumber Generator
        |--------------------------------------------------------------------------
        |
        */
        'ticket_number' => [
            /** Generator and must @implements GeneratorInterface */
            'generator'          => TicketNumberGenerator::class,

            /** Source field(column) */
            'source_column'      => 'name',

            /** Destination field(column) */
            'destination_column' => 'ticket_number',
        ],

        /*
        |--------------------------------------------------------------------------
        | OrderNumber Generator
        |--------------------------------------------------------------------------
        |
        */
        'order_number'  => [
            /** Generator and must @implements GeneratorInterface */
            'generator'          => OrderNumberGenerator::class,

            /** Source field(column) */
            'source_column'      => 'name',

            /** Destination field(column) */
            'destination_column' => 'order_number',
        ],
    ],

];
