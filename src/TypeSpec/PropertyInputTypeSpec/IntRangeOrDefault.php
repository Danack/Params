<?php

namespace TypeSpec\PropertyInputTypeSpec;

use TypeSpec\ExtractRule\GetIntOrDefault;
use TypeSpec\PropertyInputTypeSpec;
use TypeSpec\InputTypeSpec;
use TypeSpec\ProcessRule\RangeIntValue;

#[\Attribute]
class IntRangeOrDefault implements PropertyInputTypeSpec
{
    public function __construct(
        private int $minimum,
        private int $maximum,
        private string $name,
        private int $default
    ) {
    }

    public function getInputTypeSpec(): InputTypeSpec
    {
        return new InputTypeSpec(
            $this->name,
            new GetIntOrDefault($this->default),
            new RangeIntValue($this->minimum, $this->maximum),
        );
    }
}
