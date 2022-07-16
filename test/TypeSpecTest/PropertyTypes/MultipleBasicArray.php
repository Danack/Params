<?php


namespace TypeSpecTest\PropertyTypes;

use TypeSpec\ExtractRule\GetArrayOfParam;
use TypeSpec\InputTypeSpec;
use TypeSpec\PropertyInputTypeSpec;
//use ParamsTest\DTOTypes\BasicDTO;
use TypeSpecTest\PropertyTypes\Quantity;

#[\Attribute]
class MultipleBasicArray implements PropertyInputTypeSpec
{
    public function __construct(
        private string $name
    ) {
    }

    public function getInputTypeSpec(): InputTypeSpec
    {
        return new InputTypeSpec(
            $this->name,
            new GetArrayOfParam(Quantity::class),
        );
    }
}
