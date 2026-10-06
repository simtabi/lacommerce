<?php

declare(strict_types=1);

namespace Simtabi\Lacommerce\Tests\Fixtures\Generators;

use Illuminate\Database\Eloquent\Model;
use Simtabi\Lacommerce\Generators\Contracts\SkuGeneratorInterface;
use Simtabi\Lacommerce\Generators\Services\Generator;

final class FallbackSkuGenerator extends Generator implements SkuGeneratorInterface
{
    public function __construct(Model $model)
    {
        parent::__construct($model, 'skuConfigs', 'sku');
    }

    protected function getSourceString(): string
    {
        $fields = array_filter($this->model->only($this->modelConfig->getSourceColumn()));

        if ($fields === []) {
            return 'item';
        }

        return implode($this->modelConfig->getSeparator(), $fields);
    }
}
