<?php

namespace TypeSpec\PropertyInputTypeSpec;

use TypeSpec\ExtractRule\GetInt;
use TypeSpec\PropertyInputTypeSpec;
use TypeSpec\InputTypeSpec;
use TypeSpec\ProcessRule\RangeIntValue;

#[\Attribute]
/**
 * Gets an int by name from input, and checks it for minimum
 * and maximum values. If input value is not set for that name,
 * then a default value is used instead.
 */
class IntRange implements PropertyInputTypeSpec
{
    /**
     *
     * @param int $minimum The minimum value, inclusive.
     * @param int $maximum The maximum value, inclusive
     * @param string $name
     */
    public function __construct(
        private int $minimum,
        private int $maximum,
        private string $name
    ) {
    }

    public function getInputTypeSpec(): InputTypeSpec
    {
        return new InputTypeSpec(
            $this->name,
            new GetInt(),
            new RangeIntValue($this->minimum, $this->maximum),
        );
    }
}
