<?php

namespace Simtabi\Lacommerce\Generators\Concerns\OrderNumber;

use Illuminate\Database\Eloquent\Model;
use Simtabi\Lacommerce\Generators\Contracts\OrderNumberGeneratorInterface;
use Simtabi\Lacommerce\Generators\Services\Generator;

/**
 * The shipped OrderNumber generator. Final: to customise it, extend Generators\Services\Generator or implement
 * OrderNumberGeneratorInterface, and name your class in the config. See docs/tools/generators.md.
 */
final class OrderNumberGenerator extends Generator implements OrderNumberGeneratorInterface
{

    public function __construct(Model $model)
    {
        parent::__construct($model, 'orderNumberConfigs', 'orderNumber');
    }

}
