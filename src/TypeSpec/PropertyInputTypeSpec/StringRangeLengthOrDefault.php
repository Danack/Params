<?php

namespace TypeSpec\PropertyInputTypeSpec;

use TypeSpec\ExtractRule\GetStringOrDefault;
use TypeSpec\PropertyInputTypeSpec;
use TypeSpec\InputTypeSpec;
use TypeSpec\ProcessRule\RangeStringLength;

#[\Attribute]
/**
 * Gets a string by name from input, and checks it for minimum
 * and maximum length. If input value is not set for that name,
 * then a default value is used instead.
 */
class StringRangeLengthOrDefault implements PropertyInputTypeSpec
{
    public function __construct(
        private int $minimumLength,
        private int $maximumLength,
        private string $name,
        private string $default
    ) {
    }

    public function getInputTypeSpec(): InputTypeSpec
    {
        return new InputTypeSpec(
            $this->name,
            new GetStringOrDefault($this->default),
            new RangeStringLength($this->minimumLength, $this->maximumLength),
        );
    }
}
