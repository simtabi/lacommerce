<?php

namespace Simtabi\Lacommerce\Generators\Concerns\Sku;

use Simtabi\Lacommerce\Generators\Services\Observer;
use Simtabi\Lacommerce\Generators\Contracts\SkuGeneratorInterface;

final class SkuObserver extends Observer
{

    public function __construct()
    {
        parent::__construct(SkuGeneratorInterface::class, 'skuConfig');
    }

}
