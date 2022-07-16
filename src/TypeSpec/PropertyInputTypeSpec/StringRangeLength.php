<?php

namespace TypeSpec\PropertyInputTypeSpec;

use TypeSpec\ExtractRule\GetString;
use TypeSpec\PropertyInputTypeSpec;
use TypeSpec\InputTypeSpec;
use TypeSpec\ProcessRule\RangeIntValue;
use TypeSpec\ProcessRule\RangeStringLength;

#[\Attribute]
/**
 * Gets a string by name from input, and checks it for minimum
 * and maximum length.
 */
class StringRangeLength implements PropertyInputTypeSpec
{
    public function __construct(
        private int $minimumLength,
        private int $maximumLength,
        private string $name
    ) {
    }

    public function getInputTypeSpec(): InputTypeSpec
    {
        return new InputTypeSpec(
            $this->name,
            new GetString(),
            new RangeStringLength($this->minimumLength, $this->maximumLength),
        );
    }
}
