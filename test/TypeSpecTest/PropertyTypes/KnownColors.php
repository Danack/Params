<?php


namespace TypeSpecTest\PropertyTypes;

use TypeSpec\ExtractRule\GetStringOrDefault;
use TypeSpec\ProcessRule\Enum;
use TypeSpec\InputTypeSpec;
use TypeSpec\PropertyInputTypeSpec;

#[\Attribute]
class KnownColors implements PropertyInputTypeSpec
{
    const KNOWN_COLORS = ['red', 'green', 'blue'];

    public function __construct(
        private string $name
    ) {
    }

    public function getInputTypeSpec(): InputTypeSpec
    {
        return new InputTypeSpec(
            $this->name,
            new GetStringOrDefault('blue'),
            new Enum(self::KNOWN_COLORS)
        );
    }
}
