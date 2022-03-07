<?php

use Simtabi\Lacommerce\Generators\Services\Contracts\GeneratorInterface;
use Simtabi\Lacommerce\Generators\Concerns\Sku\SkuGenerator;
use Simtabi\Lacommerce\Generators\Concerns\TicketNumber\TicketNumberGenerator;
use Simtabi\Lacommerce\Generators\Concerns\OrderNumber\OrderNumberGenerator;

return [

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
