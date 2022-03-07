<?php

namespace Simtabi\Lacommerce\Generators\Services;

use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Simtabi\Lacommerce\Generators\Services\Contracts\GeneratorInterface;

class Generator implements Jsonable, Renderable, GeneratorInterface
{
    /**
     * Model to generate values from.
     *
     * @var Model
     */
    protected Model $model;

    /**
     * Shortcut to the Options.
     *
     * @var Configs
     */
    protected Configs $modelConfig;

    /**
     * Str mixin
     *
     * @var string
     */
    protected string $strMixin;

    /**
     * Create new Generator.
     *
     * @param Model $model
     * @param string $modelConfigMethod
     * @param string $strMixin
     */
    public function __construct(Model $model, string $modelConfigMethod, string $strMixin)
    {

        $this->modelConfig = $model->{$modelConfigMethod}();
        $this->strMixin    = $strMixin;
        $this->model       = $model;

    }

    /**
     * Render the Generator.
     *
     * @return string
     */
    public function render(): string
    {
        // Fetch the part that makes the initial source
        $source = $this->getSourceString();

        // now, generate the value
        return $this->generate($source, $this->modelConfig->separator, $this->modelConfig->forceUnique);
    }

    /**
     * Get the source fields for the generated value.
     *
     * @return string
     */
    protected function getSourceString(): string
    {
        // fetch the source fields
        $source = $this->modelConfig->sourceColumn;

        // Fetch fields from model, skip empty
        $fields = array_filter($this->model->only($source));

        // Implode with a separator
        return implode($this->modelConfig->separator, $fields);
    }

    /**
     * Generate the value.
     *
     * @param  string  $source
     * @param  string  $separator
     * @param  bool  $unique
     * @return string
     */
    protected function generate(string $source, string $separator, bool $unique = false): string
    {
        // Make
        $value = Str::{$this->strMixin}($source, $separator);

        // if we are forcing uniques, and it already exists, re-try
        if ($unique and $this->exists($value)) {
            return $this->generate($source, $separator, $unique);
        }

        return $value;
    }

    /**
     * True if the value already exists in the DB.
     *
     * @param  string  $value
     * @return bool
     */
    protected function exists(string $value): bool
    {
        return $this->model
            ->whereKeyNot($this->model->getKey())
            ->where($this->modelConfig->destinationColumn, $value)
            ->withoutGlobalScopes()
            ->exists();
    }

    /**
     * Convert the Generator to String.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return $this->render();
    }
}
