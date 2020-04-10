<?php

declare(strict_types = 1);

namespace Params;

/**
 * Holds variables that have been previously processed by the validator.
 * This allows later InputParameters to reference earlier InputParameters.
 * This is useful for things like checking two parameters are equal
 * e.g. to check an email address has been entered twice identically.
 */
interface ProcessedValues
{
    /**
     * Is the value available.
     *
     * @param string $name
     * @return bool
     */
    public function hasValue(string $name): bool;

    /**
     * Gets the processed value
     *
     * @param string $name
     * @return mixed
     */
    public function getValue(string $name);
}
