<?php


namespace ParamsTest\PropertyTypes;

use Type\ExtractRule\GetArrayOfParam;
use Type\PropertyDefinition;
use Type\TypeProperty;
//use ParamsTest\DTOTypes\BasicDTO;
use ParamsTest\PropertyTypes\Quantity;

#[\Attribute]
class MultipleBasicArray implements TypeProperty
{
    public function __construct(
        private string $name
    ) {
    }

    public function getPropertyRules(): PropertyDefinition
    {
        return new PropertyDefinition(
            $this->name,
            new GetArrayOfParam(Quantity::class),
        );
    }
}
