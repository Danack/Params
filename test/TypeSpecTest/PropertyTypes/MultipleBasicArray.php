<?php


namespace TypeSpecTest\PropertyTypes;

use TypeSpec\ExtractRule\GetArrayOfParam;
use TypeSpec\InputTypeSpec;
use TypeSpec\TypeProperty;
//use ParamsTest\DTOTypes\BasicDTO;
use TypeSpecTest\PropertyTypes\Quantity;

#[\Attribute]
class MultipleBasicArray implements TypeProperty
{
    public function __construct(
        private string $name
    ) {
    }

    public function getPropertyRules(): InputTypeSpec
    {
        return new InputTypeSpec(
            $this->name,
            new GetArrayOfParam(Quantity::class),
        );
    }
}
