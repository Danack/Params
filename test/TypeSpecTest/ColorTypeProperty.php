<?php

declare(strict_types=1);


namespace TypeSpecTest;

use TypeSpec\TypeProperty;
use TypeSpec\InputTypeSpec;
use TypeSpec\ExtractRule\GetStringOrDefault;
use TypeSpec\ProcessRule\IsRgbColor;

class ColorTypeProperty implements TypeProperty
{
    public function __construct(
        private string $name,
        private string $default
    ) {
    }

    public function getPropertyRules(): InputTypeSpec
    {
        return new InputTypeSpec(
            $this->name,
            new GetStringOrDefault($this->default),
            new IsRgbColor()
        );
    }
}
