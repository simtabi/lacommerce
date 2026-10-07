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
     * Leading part of the generated value; null for none.
     *
     * @var ?string
     */
    protected ?string      $prefix = null;

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
            ->setPrefix($config[$key]['prefix'] ?? null)
            ->setSeparator($default['separator'])
            ->forceUnique($default['unique'])
            ->generateOnCreate($default['generate_on_create'])
            ->refreshOnUpdate($default['refresh_on_update']);

    }

    /**
     * Resolve the class this is called on from the container.
     *
     * Call it on a subclass the container can build, such as SkuConfigs::make(). The base class cannot be
     * resolved, because its constructor needs a config array and a key, so calling make() on it throws an
     * exception naming the subclasses rather than a container error about an unresolvable `array $config`.
     *
     * @return ConfigsInterface
     *
     * @throws InvalidOptionException when called on Configs itself
     */
    public static function make(): ConfigsInterface
    {
        if (static::class === self::class) {
            throw InvalidOptionException::invalidArgument(sprintf(
                '%s::make() cannot build the base class; call it on a subclass bound in the container, such as '
                . 'SkuConfigs::make(), OrderNumberConfigs::make() or TicketNumberConfigs::make().',
                self::class,
            ));
        }

        return resolve(static::class);
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
     * Set the prefix: a leading part of the generated value. Null or an empty string for none. For an
     * order number it replaces the default `ORD`.
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
     * @param  string  $destinationColumn
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
