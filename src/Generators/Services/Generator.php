<?php

namespace Simtabi\Lacommerce\Generators\Services;

use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Simtabi\Lacommerce\Generators\Services\Contracts\GeneratorInterface;
use Simtabi\Lacommerce\Providers\LacommerceServiceProvider;
use Simtabi\Lacommerce\Supports\Identifiers;

/**
 * The base every shipped generator extends, and the one a custom generator should extend.
 *
 * The provider builds a generator as `new $class($model)`, so a subclass declares a one-argument
 * constructor and passes the model, the trait's config method (`skuConfigs`, `orderNumberConfigs`,
 * `ticketNumberConfigs`) and the identifier kind (`sku`, `orderNumber`, `ticketNumber`) to this one.
 * render() then builds the source with getSourceString(), makes a candidate with makeValue(), and, when
 * the config forces uniqueness, retries while exists() finds the candidate in the destination column.
 * Override any of those protected hooks; the shipped subclasses are final and are not the seam.
 */
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
        return $this->generate($source, $this->modelConfig->getSeparator(), $this->modelConfig->isForceUnique());
    }

    /**
     * Get the source fields for the generated value.
     *
     * @return string
     */
    protected function getSourceString(): string
    {
        // fetch the source fields
        $source = $this->modelConfig->getSourceColumn();

        // Fetch fields from model, skip empty
        $fields = array_filter($this->model->only($source));

        // Implode with a separator
        return implode($this->modelConfig->getSeparator(), $fields);
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
        $value = $this->makeValue($source, $separator);

        // if we are forcing uniques, and it already exists, re-try
        if ($unique and $this->exists($value)) {
            return $this->generate($source, $separator, $unique);
        }

        return $value;
    }

    /**
     * Make one candidate value.
     *
     * The three shipped generators call Identifiers directly rather than the `Str` macro named by
     * `$strMixin`: Str's macro registry is a flat, host-owned map, so going through it let any package
     * or application that registered its own `Str::sku()` replace what every model generated. A
     * subclass naming any other `$strMixin` still has that macro called, as before.
     *
     * An empty separator falls back to the configured default, as the 0.1.0 macros did. The configured
     * prefix, when there is one, leads the value; for an order number it replaces `ORD`.
     *
     * @param  string  $source
     * @param  string  $separator
     * @return string
     */
    protected function makeValue(string $source, string $separator): string
    {
        $separator = $separator ?: (string) config(
            LacommerceServiceProvider::CONFIG_KEY . '.generator.default.separator',
            Identifiers::DEFAULT_SEPARATOR,
        );

        $prefix = $this->modelConfig->getPrefix();

        return match ($this->strMixin) {
            'sku'          => Identifiers::sku($source, $separator, $prefix),
            'orderNumber'  => Identifiers::orderNumber($prefix, $separator),
            'ticketNumber' => Identifiers::ticketNumber($source, $separator, $prefix),
            default        => Str::{$this->strMixin}($source, $separator),
        };
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
            ->where($this->modelConfig->getDestinationColumn(), $value)
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
