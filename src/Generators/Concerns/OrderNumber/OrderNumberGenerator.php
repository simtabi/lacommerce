<?php

namespace Simtabi\Lacommerce\Generators\Concerns\OrderNumber;

use Illuminate\Database\Eloquent\Model;
use Simtabi\Lacommerce\Generators\Services\Generator;

final class OrderNumberGenerator extends Generator
{

    public function __construct(Model $model)
    {
        parent::__construct($model, 'orderNumberConfigs', 'orderNumber');
    }

}
