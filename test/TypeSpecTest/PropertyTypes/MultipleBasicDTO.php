<?php


namespace TypeSpecTest\PropertyTypes;

use TypeSpec\ExtractRule\GetArrayOfType;
use TypeSpec\InputTypeSpec;
use TypeSpec\TypeProperty;
use TypeSpecTest\DTOTypes\BasicDTO;

#[\Attribute]
class MultipleBasicDTO implements TypeProperty
{
    public function __construct(
        private string $name
    ) {
    }

    public function getPropertyRules(): InputTypeSpec
    {
        return new InputTypeSpec(
            $this->name,
            new GetArrayOfType(BasicDTO::class),
        );
    }
}
