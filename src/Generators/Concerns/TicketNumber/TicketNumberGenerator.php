<?php

namespace Simtabi\Lacommerce\Generators\Concerns\TicketNumber;

use Illuminate\Database\Eloquent\Model;
use Simtabi\Lacommerce\Generators\Services\Generator;

final class TicketNumberGenerator extends Generator
{

    public function __construct(Model $model)
    {
        parent::__construct($model, 'ticketNumberConfigs', 'ticketNumber');
    }

}
