<?php

declare(strict_types=1);


namespace ParamsTest;

use Attribute;
use Type\TypeProperty;
use Type\PropertyDefinition;
use Type\ExtractRule\GetStringOrDefault;
use Type\ProcessRule\ImagickIsRgbColor;

#[Attribute(Attribute::TARGET_PROPERTY|Attribute::IS_REPEATABLE)]
class ImagickColorTypeProperty implements TypeProperty
{
    public function __construct(
        private string $default,
        private string $name
    ) {
    }

    public function getPropertyRules(): PropertyDefinition
    {
        return new PropertyDefinition(
            $this->name,
            new GetStringOrDefault($this->default),
            new ImagickIsRgbColor()
        );
    }
}
