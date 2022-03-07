<?php

namespace Simtabi\Lacommerce\Generators\Concerns\Sku;

use Illuminate\Database\Eloquent\Model;
use Simtabi\Lacommerce\Generators\Services\Generator;

final class SkuGenerator extends Generator
{

    public function __construct(Model $model)
    {
        parent::__construct($model, 'skuConfigs', 'sku');
    }

}
