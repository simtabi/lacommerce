<?php

declare(strict_types=1);

namespace Simtabi\Lacommerce\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;
use Simtabi\Lacommerce\Traits\HasSku;

final class DummyProduct extends Model
{
    use HasSku;

    protected $table = 'dummy_models';

    protected $guarded = [];
}
