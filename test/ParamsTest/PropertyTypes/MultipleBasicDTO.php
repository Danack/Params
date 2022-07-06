<?php


namespace ParamsTest\PropertyTypes;

use Type\ExtractRule\GetArrayOfType;
use Type\PropertyDefinition;
use Type\TypeProperty;
use ParamsTest\DTOTypes\BasicDTO;

#[\Attribute]
class MultipleBasicDTO implements TypeProperty
{
    public function __construct(
        private string $name
    ) {
    }

    public function getPropertyRules(): PropertyDefinition
    {
        return new PropertyDefinition(
            $this->name,
            new GetArrayOfType(BasicDTO::class),
        );
    }
}
