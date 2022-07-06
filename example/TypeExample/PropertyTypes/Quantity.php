<?php

namespace TypeExample\PropertyTypes;

use Type\ExtractRule\GetInt;
use Type\PropertyDefinition;
use Type\TypeProperty;
use Type\ProcessRule\MaxIntValue;
use Type\ProcessRule\MinIntValue;

#[\Attribute]
class Quantity implements TypeProperty
{
    public function __construct(
        private string $name
    ) {
    }

    public function getPropertyRules(): PropertyDefinition
    {
        return new PropertyDefinition(
            $this->name,
            new GetInt(),
            new MinIntValue(1),
            new MaxIntValue(20),
        );
    }
}
