<?php


namespace ParamsTest\PropertyTypes;

use Type\ExtractRule\GetStringOrDefault;
use Type\ProcessRule\Enum;
use Type\PropertyDefinition;
use Type\TypeProperty;

#[\Attribute]
class KnownColors implements TypeProperty
{
    const KNOWN_COLORS = ['red', 'green', 'blue'];

    public function __construct(
        private string $name
    ) {
    }

    public function getPropertyRules(): PropertyDefinition
    {
        return new PropertyDefinition(
            $this->name,
            new GetStringOrDefault('blue'),
            new Enum(self::KNOWN_COLORS)
        );
    }
}
