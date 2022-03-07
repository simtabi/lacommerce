<?php

namespace Simtabi\Lacommerce\Generators\Services\Contracts;

/**
 * @property-read string[] $sourceColumn
 * @property-read string $destinationColumn
 * @property-read bool $status
 * @property-read string $separator
 * @property-read bool $generateOnCreate
 * @property-read bool $refreshOnUpdate
 */
interface ConfigsInterface
{

    /**
     * Create a new instance of the class, with standard settings.
     *
     * @return self
     */
    public static function make(): self;

    /**
     * Set the source column.
     *
     * @param array|string $sourceColumn
     * @return $this
     */
    public function setSourceColumn(array|string $sourceColumn): self;

    /**
     * @return array|string
     */
    public function getSourceColumn(): array|string;

    /**
     * Set the prefix.
     *
     * @param mixed $prefix
     * @return $this
     */
    public function setPrefix(string $prefix): self;

    /**
     * @return ?string
     */
    public function getPrefix(): ?string;

    /**
     * Set the destination column.
     *
     * @param  mixed  $destinationColumn
     * @return $this
     */
    public function setDestinationColumn(string $destinationColumn): self;

    /**
     * @return string
     */
    public function getDestinationColumn(): string;

    /**
     * Set unique flag.
     *
     * @param bool $status
     * @return self
     */
    public function forceUnique(bool $status): self;

    /**
     * @return bool
     */
    public function isForceUnique(): bool;

    /**
     * Set the separator value.
     *
     * @return self
     */
    public function allowDuplicates(): self;

    /**
     * Set the separator value.
     *
     * @param  string  $separator
     * @return self
     */
    public function setSeparator(string $separator): self;

    /**
     * @return string
     */
    public function getSeparator(): string;

    /**
     * Set the generateOnCreate value.
     *
     * @param  bool  $status
     * @return self
     */
    public function generateOnCreate(bool $status): self;

    /**
     * @return bool
     */
    public function isGenerateOnCreate(): bool;

    /**
     * Set the refreshOnUpdate value.
     *
     * @param  bool  $status
     * @return self
     */
    public function refreshOnUpdate(bool $status): self;

    /**
     * @return bool
     */
    public function isRefreshOnupdate(): bool;

}
