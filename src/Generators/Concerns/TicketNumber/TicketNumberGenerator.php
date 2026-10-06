<?php

namespace Simtabi\Lacommerce\Generators\Concerns\TicketNumber;

use Illuminate\Database\Eloquent\Model;
use Simtabi\Lacommerce\Generators\Contracts\TicketNumberGeneratorInterface;
use Simtabi\Lacommerce\Generators\Services\Generator;

/**
 * The shipped TicketNumber generator. Final: to customise it, extend Generators\Services\Generator or implement
 * TicketNumberGeneratorInterface, and name your class in the config. See docs/tools/generators.md.
 */
final class TicketNumberGenerator extends Generator implements TicketNumberGeneratorInterface
{

    public function __construct(Model $model)
    {
        parent::__construct($model, 'ticketNumberConfigs', 'ticketNumber');
    }

}
