# Lacommerce — A collection handy helper functions for your laravel ecommerce projects


- ### SKU generator

Generate unique SKUs when saving any Eloquent model with support for Laravel 8 and above.

```php
$model       = new EloquentModel();
$model->name = 'Laravel is Awesome';
$model->save();

echo $model->sku; // outputs "LAR-80564492"
```

Package will add a new method to Laravel's `Illuminate\Support\Str::sku()` class to generate an SKU for you.

## Installation

You can install the package via composer:

```bash
composer require simtabi/lacommerce
```

The service provider will automatically register itself.

You can publish the config file with:
```bash
# publish config files
php artisan vendor:publish --tag=lacommerce:config

# publish assets
php artisan vendor:publish --tag=lacommerce:assets

# publish view files
php artisan vendor:publish --tag=lacommerce:views
```

This is the contents of the config file that will be published at `config/lacommerce.php`:

```php

use Simtabi\Lacommerce\Sequencing\Concerns\SkuGenerator;
use Simtabi\Lacommerce\Sequencing\Concerns\TicketNumberGenerator;
use Simtabi\Lacommerce\Sequencing\Concerns\OrderNumberGenerator;

return [
    /*
    |--------------------------------------------------------------------------
    | Generator settings
    |--------------------------------------------------------------------------
    |
    */

    'generator' => [
        'default' => [

            /** Separator */
            'separator'          => '-',

            /** Enforce generated values to be unique */
            'unique'             => true,

            /** Generate on create */
            'generate_on_create' => true,

            /** Refresh on update */
            'refresh_on_update'  => true,

        ],

        /*
        |--------------------------------------------------------------------------
        | SKU Generator
        |--------------------------------------------------------------------------
        |
        */
        'sku'           => [
            /** Generator and must @implements GeneratorInterface */
            'generator'   => SkuGenerator::class,

            /** Source field(column) */
            'source'      => 'name',

            /** Destination field(column) */
            'destination' => 'sku',
        ],

        /*
        |--------------------------------------------------------------------------
        | TicketNumber Generator
        |--------------------------------------------------------------------------
        |
        */
        'ticket_number' => [
            /** Generator and must @implements GeneratorInterface */
            'generator'   => TicketNumberGenerator::class,

            /** Source field(column) */
            'source'      => 'name',

            /** Destination field(column) */
            'destination' => 'ticket_number',
        ],

        /*
        |--------------------------------------------------------------------------
        | OrderNumber Generator
        |--------------------------------------------------------------------------
        |
        */
        'order_number'  => [
            /** Generator and must @implements GeneratorInterface */
            'generator'   => OrderNumberGenerator::class,

            /** Source field(column) */
            'source'      => 'name',

            /** Destination field(column) */
            'destination' => 'order_number',
        ],
    ],

];

```

Please note that the above set up expects you have an `sku` field in your model. If you plan to manually overwrite the values, please make sure to add this field to `fillable` array;

### Usage

Add `Simtabi\Lacommerce\Traits\HasSku` trait to your model. That's it!

```php
namespace App;

use Simtabi\Lacommerce\Traits\HasSku;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasSku;
}
```

Behind the scenes this will register an observer for the `sku` field, which will be generated every time you save the model.

## Advanced usage

If you want to change settings for a specific model, you can overload the `skuConfigs`() method:

```php
namespace App;

use Simtabi\Lacommerce\Generators\Concerns\Sku\SkuConfigs;
use Simtabi\Lacommerce\Traits\HasSku;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasSku;

    /**
     * Get the options for generating the Sku.
     *
     * @return SkuConfigs
     */
    public function skuConfigs() : SkuConfigs
    {
        return SkuConfigs::make()
            ->setSourceColumn(['id', 'user_id'])
            ->setDestinationColumn('order_number')
            ->setSeparator('-')
            ->forceUnique(true)
            ->generateOnCreate(true)
            ->refreshOnUpdate(false);
    }
}
```

### Custom Generator

Assuming you want some extra logic, like having a default value, or defining prefix for an SKU,
you can implement your own SkuGenerator. It is easiest to extend the base class, but you are free to explore any which way.

First, create a custom class:

```php

namespace App\Components\SkuGenerator;

use Simtabi\Lacommerce\Generators\Concerns\Sku\SkuGenerator;

class CustomSkuGenerator extends SkuGenerator
{
    /**
     * Get the source fields for the SKU.
     *
     * @return string
     */
    protected function getSourceString(): string
    {
        // fetch the source fields
        $source = $this->configs->source;
        // Fetch fields from model, skip empty
        $fields = array_filter($this->model->only($source));
        // Fetch fields from the model, if empty, use custom logic to resolve
        if (empty($fields)) {
            return 'some-random-value-logic';
        }
        // Implode with a separator
        return implode($this->configs->separator, $fields);
    }
}
```

and then update `generator` config value:

```php
    'generator' => \App\Components\SkuGenerator\CustomSkuGenerator::class,
```

You can also opt out to change implementation completely;
just remember that custom generator must implement `Simtabi\Lacommerce\SKU\Contracts\SkuGenerator`.

### About SKUs

[Stock Keeping Unit](https://en.wikipedia.org/wiki/Stock_keeping_unit) allows you to set a unique identifier or code that refers to the particular stock keeping unit.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information about what has changed recently.

## Testing

```bash
composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Cyrill Kalita](https://github.com/binary-cats)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
