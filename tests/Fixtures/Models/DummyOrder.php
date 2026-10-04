<?php

declare(strict_types=1);

namespace Simtabi\Lacommerce\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;
use Simtabi\Lacommerce\Traits\HasOrderNumber;

final class DummyOrder extends Model
{
    use HasOrderNumber;

    protected $table = 'dummy_models';

    protected $guarded = [];
}
