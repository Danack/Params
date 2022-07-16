<?php


namespace TypeSpecExample\PropertyTypes;

use TypeSpec\ExtractRule\GetStringOrDefault;
use TypeSpec\ProcessRule\Enum;
use TypeSpec\InputTypeSpec;
use TypeSpec\PropertyInputTypeSpec;

#[\Attribute]
class KnownColors implements PropertyInputTypeSpec
{
    public function __construct(
        private string $name
    ) {
    }

    public function getInputTypeSpec(): InputTypeSpec
    {
        return new InputTypeSpec(
            $this->name,
            new GetStringOrDefault('blue'),
            new Enum(['red', 'green', 'blue'])
        );
    }
}
