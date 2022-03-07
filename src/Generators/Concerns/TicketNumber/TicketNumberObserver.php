<?php

namespace Simtabi\Lacommerce\Generators\Concerns\TicketNumber;

use Simtabi\Lacommerce\Generators\Services\Observer;
use Simtabi\Lacommerce\Generators\Contracts\SkuGeneratorInterface;
use Simtabi\Lacommerce\Generators\Contracts\TicketNumberGeneratorInterface;

final class TicketNumberObserver extends Observer
{

    public function __construct()
    {
        parent::__construct(TicketNumberGeneratorInterface::class, 'ticketNumberConfig');
    }

}
