<?php

namespace Simtabi\Lacommerce\Generators\Services\Contracts;

interface GeneratorInterface
{
    /**
     * Render the Generator.
     *
     * @return string
     */
    public function render(): string;
}
