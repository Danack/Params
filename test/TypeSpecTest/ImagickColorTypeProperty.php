<?php

declare(strict_types=1);


namespace TypeSpecTest;

use Attribute;
use TypeSpec\TypeProperty;
use TypeSpec\InputTypeSpec;
use TypeSpec\ExtractRule\GetStringOrDefault;
use TypeSpec\ProcessRule\ImagickIsRgbColor;

#[Attribute(Attribute::TARGET_PROPERTY|Attribute::IS_REPEATABLE)]
class ImagickColorTypeProperty implements TypeProperty
{
    public function __construct(
        private string $default,
        private string $name
    ) {
    }

    public function getPropertyRules(): InputTypeSpec
    {
        return new InputTypeSpec(
            $this->name,
            new GetStringOrDefault($this->default),
            new ImagickIsRgbColor()
        );
    }
}
