<?php

declare(strict_types=1);


namespace ParamsTest;

use Type\TypeProperty;
use Type\PropertyDefinition;
use Type\ExtractRule\GetStringOrDefault;
use Type\ProcessRule\IsRgbColor;

class ColorTypeProperty implements TypeProperty
{
    public function __construct(
        private string $name,
        private string $default
    ) {
    }

    public function getPropertyRules(): PropertyDefinition
    {
        return new PropertyDefinition(
            $this->name,
            new GetStringOrDefault($this->default),
            new IsRgbColor()
        );
    }
}
