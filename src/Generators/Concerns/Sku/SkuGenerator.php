<?php

namespace Simtabi\Lacommerce\Generators\Concerns\Sku;

use Illuminate\Database\Eloquent\Model;
use Simtabi\Lacommerce\Generators\Contracts\SkuGeneratorInterface;
use Simtabi\Lacommerce\Generators\Services\Generator;

/**
 * The shipped Sku generator. Final: to customise it, extend Generators\Services\Generator or implement
 * SkuGeneratorInterface, and name your class in the config. See docs/tools/generators.md.
 */
final class SkuGenerator extends Generator implements SkuGeneratorInterface
{

    public function __construct(Model $model)
    {
        parent::__construct($model, 'skuConfigs', 'sku');
    }

}
