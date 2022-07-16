<?php


namespace TypeSpecTest\PropertyTypes;

use TypeSpec\ExtractRule\GetArrayOfType;
use TypeSpec\InputTypeSpec;
use TypeSpec\PropertyInputTypeSpec;
use TypeSpecTest\DTOTypes\BasicDTO;

#[\Attribute]
class MultipleBasicDTO implements PropertyInputTypeSpec
{
    public function __construct(
        private string $name
    ) {
    }

    public function getInputTypeSpec(): InputTypeSpec
    {
        return new InputTypeSpec(
            $this->name,
            new GetArrayOfType(BasicDTO::class),
        );
    }
}
