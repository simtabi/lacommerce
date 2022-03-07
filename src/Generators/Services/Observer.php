<?php

namespace Simtabi\Lacommerce\Generators\Services;

use Illuminate\Database\Eloquent\Model;
use Simtabi\Lacommerce\Generators\Services\Contracts\GeneratorInterface;

class Observer
{

    /**
     * Generator Interface class
     *
     * @var string
     */
    private string $generatorInterface;

    /**
     * Method that's used to fetch configs
     *
     * @var string
     */
    private string $configMethod;

    public function __construct(string $generatorInterface, string $configMethod)
    {
        $this->generatorInterface = $generatorInterface;
        $this->configMethod       = $configMethod;
    }


    /**
     * Make the Generator.
     *
     * @param Model $model
     * @return GeneratorInterface
     */
    protected function generator(Model $model): GeneratorInterface
    {
        return resolve($this->generatorInterface, ['model' => $model]);
    }

    /**
     * Handle model "creating" event.
     *
     * @param Model $model
     * @return void
     */
    public function creating(Model $model): void
    {
        // Name of the destination column to store the generated value
        $destination = $model->{$this->configMethod}('destinationColumn');

        // Set the value
        if ($model->{$this->configMethod}('generateOnCreate')) {
            $model->setAttribute($destination, (string) $this->generator($model));
        }
    }

    /**
     * Handle model "updating" event.
     *
     * @param Model $model
     * @return void
     */
    public function updating(Model $model): void
    {
        // Name of the destination column to store the generated value
        $destination = $model->{$this->configMethod}('destinationColumn');

        // If we are overwriting manually, just return
        if ($model->isDirty($destination)) {
            return;
        }

        // Fetch the source of the value to generated
        $source = $model->{$this->configMethod}('sourceColumn');

        // if we are requested to generate and those fields that are dirty
        if ($model->{$this->configMethod}('refreshOnUpdate') and $model->isDirty($source)) {
            $model->setAttribute($destination, (string) $this->generator($model));
        }
    }

}
