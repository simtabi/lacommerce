<?php

namespace Simtabi\Lacommerce\Generators\Concerns\OrderNumber;

use Simtabi\Lacommerce\Generators\Services\Configs;

final class OrderNumberConfigs extends Configs
{

    /**
     * Create new class.
     */
    public function __construct(array $config, string $key)
    {
        parent::__construct($config, $key);
    }

    /**
     * Create a new instance of the class, with standard settings.
     *
     * @return self instance
     */
    public static function make(): self
    {
        return resolve(self::class);
    }

}
