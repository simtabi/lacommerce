<?php

declare(strict_types=1);

namespace Simtabi\Lacommerce\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;
use Simtabi\Lacommerce\Traits\HasTicketNumber;

final class DummyTicket extends Model
{
    use HasTicketNumber;

    protected $table = 'dummy_models';

    protected $guarded = [];
}
