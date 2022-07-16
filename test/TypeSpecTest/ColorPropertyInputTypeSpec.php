<?php

declare(strict_types=1);


namespace TypeSpecTest;

use TypeSpec\PropertyInputTypeSpec;
use TypeSpec\InputTypeSpec;
use TypeSpec\ExtractRule\GetStringOrDefault;
use TypeSpec\ProcessRule\IsRgbColor;

class ColorPropertyInputTypeSpec implements PropertyInputTypeSpec
{
    public function __construct(
        private string $name,
        private string $default
    ) {
    }

    public function getInputTypeSpec(): InputTypeSpec
    {
        return new InputTypeSpec(
            $this->name,
            new GetStringOrDefault($this->default),
            new IsRgbColor()
        );
    }
}
