<?php

namespace TypeSpecExample\PropertyTypes;

use TypeSpec\ExtractRule\GetInt;
use TypeSpec\InputTypeSpec;
use TypeSpec\PropertyInputTypeSpec;
use TypeSpec\ProcessRule\MaxIntValue;
use TypeSpec\ProcessRule\MinIntValue;

#[\Attribute]
class Quantity implements PropertyInputTypeSpec
{
    public function __construct(
        private string $name
    ) {
    }

    public function getInputTypeSpec(): InputTypeSpec
    {
        return new InputTypeSpec(
            $this->name,
            new GetInt(),
            new MinIntValue(1),
            new MaxIntValue(20),
        );
    }
}
