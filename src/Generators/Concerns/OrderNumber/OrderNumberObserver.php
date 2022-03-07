<?php

namespace Simtabi\Lacommerce\Generators\Concerns\OrderNumber;

use Simtabi\Lacommerce\Generators\Services\Observer;
use Simtabi\Lacommerce\Generators\Contracts\OrderNumberGeneratorInterface;

final class OrderNumberObserver extends Observer
{

    public function __construct()
    {
        parent::__construct(OrderNumberGeneratorInterface::class, 'orderNumberConfig');
    }

}
