<?php

namespace Simtabi\Lacommerce\Generators\Services;

use Simtabi\Lacommerce\Generators\Services\Contracts\ConfigsInterface;
use Simtabi\Lacommerce\Generators\Exceptions\InvalidOptionException;
use Illuminate\Support\Arr;

class Configs implements ConfigsInterface
{

    /**
     * Set the source column which is a base for the generator.
     *
     * @var string|array
     */
    protected string|array $sourceColumn;

    /**
     * Name of the model column to store the generated value.
     *
     * @var string
     */
    protected string       $destinationColumn;

    /**
     * Define prefix.
     *
     * @var ?string
     */
    protected ?string      $prefix;

    /**
     * True if generated value is to be unique.
     *
     * @var bool
     */
    protected bool         $forceUnique = true;

    /**
     * Separator value.
     *
     * @var string
     */
    protected string       $separator = '-';

    /**
     * True if generated value to be generated on creating.
     *
     * @var bool
     */
    protected bool         $generateOnCreate;

    /**
     * True if re-fresh on update.
     *
     * @var bool
     */
    protected bool         $refreshOnUpdate;

    /**
     * Create new class.
     */
    public function __construct(array $config, string $key)
    {

        $default = $config['default'];

        $this->setSourceColumn($config[$key]['source_column'])
            ->setDestinationColumn($config[$key]['destination_column'])
            ->setSeparator($default['separator'])
            ->forceUnique($default['unique'])
            ->generateOnCreate($default['generate_on_create'])
            ->refreshOnUpdate($default['refresh_on_update']);

    }

    /**
     * @return ConfigsInterface
     */
    public static function make(): ConfigsInterface
    {
        return resolve(self::class);
    }

    /**
     * Set the source column.
     *
     * @param array|string $sourceColumn
     * @return $this
     */
    public function setSourceColumn(array|string $sourceColumn): self
    {
        $this->sourceColumn = array_filter(Arr::wrap($sourceColumn));

        return $this;
    }

    /**
     * @return array|string
     */
    public function getSourceColumn(): array|string
    {
        return $this->sourceColumn;
    }

    /**
     * Set the prefix.
     *
     * @param mixed $prefix
     * @return $this
     */
    public function setPrefix(mixed $prefix): self
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * @return ?string
     */
    public function getPrefix(): ?string
    {
        return $this->prefix;
    }

    /**
     * Set the destination column.
     *
     * @param  mixed  $destinationColumn
     * @return $this
     */
    public function setDestinationColumn(string $destinationColumn): self
    {
        $this->destinationColumn = $destinationColumn;

        return $this;
    }

    /**
     * @return string
     */
    public function getDestinationColumn(): string
    {
        return $this->destinationColumn;
    }

    /**
     * Set unique flag.
     *
     * @param bool $status
     * @return self
     */
    public function forceUnique(bool $status): self
    {
        $this->forceUnique = $status;

        return $this;
    }

    /**
     * @return bool
     */
    public function isForceUnique(): bool
    {
        return $this->forceUnique;
    }

    /**
     * Set the separator value.
     *
     * @return self
     */
    public function allowDuplicates(): self
    {
        return $this->forceUnique(false);
    }

    /**
     * Set the separator value.
     *
     * @param  string  $separator
     * @return self
     */
    public function setSeparator(string $separator): self
    {
        $this->separator = $separator;

        return $this;
    }

    /**
     * @return string
     */
    public function getSeparator(): string
    {
        return $this->separator;
    }

    /**
     * Set the generateOnCreate value.
     *
     * @param  bool  $status
     * @return self
     */
    public function generateOnCreate(bool $status): self
    {
        $this->generateOnCreate = $status;

        return $this;
    }

    /**
     * @return bool
     */
    public function isGenerateOnCreate(): bool
    {
        return $this->generateOnCreate;
    }

    /**
     * Set the refreshOnUpdate value.
     *
     * @param  bool  $status
     * @return self
     */
    public function refreshOnUpdate(bool $status): self
    {
        $this->refreshOnUpdate = $status;

        return $this;
    }

    /**
     * @return bool
     */
    public function isRefreshOnupdate(): bool
    {
        return $this->refreshOnUpdate;
    }

    /**
     * Access protected properties.
     *
     * @param string $methodOrProperty
     * @return mixed
     *
     * @throws InvalidOptionException
     */
    public function __get(string $methodOrProperty)
    {
        if (property_exists($this, $methodOrProperty))
        {
            return $this->{$methodOrProperty};
        }

        throw InvalidOptionException::invalidArgument("`{$methodOrProperty}` does not exist as a configuration property.", 500);
    }

}
