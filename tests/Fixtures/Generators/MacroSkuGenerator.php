<?php

declare(strict_types=1);

namespace Simtabi\Lacommerce\Tests\Fixtures\Generators;

use Illuminate\Database\Eloquent\Model;
use Simtabi\Lacommerce\Generators\Contracts\SkuGeneratorInterface;
use Simtabi\Lacommerce\Generators\Services\Generator;

/**
 * A custom generator naming its own `Str` macro as `$strMixin`, which makeValue() calls as its fallback.
 */
final class MacroSkuGenerator extends Generator implements SkuGeneratorInterface
{
    public const MACRO = 'lacommerceTestsMacroSku';

    public function __construct(Model $model)
    {
        parent::__construct($model, 'skuConfigs', self::MACRO);
    }
}
