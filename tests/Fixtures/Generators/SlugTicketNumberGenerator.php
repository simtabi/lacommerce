<?php

declare(strict_types=1);

namespace Simtabi\Lacommerce\Tests\Fixtures\Generators;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Simtabi\Lacommerce\Generators\Contracts\TicketNumberGeneratorInterface;

final class SlugTicketNumberGenerator implements TicketNumberGeneratorInterface
{
    public function __construct(private readonly Model $model)
    {
    }

    public function render(): string
    {
        return 'TKT-' . Str::upper(Str::slug((string) $this->model->getAttribute('name')));
    }
}
