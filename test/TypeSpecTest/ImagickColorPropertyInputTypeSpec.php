<?php

declare(strict_types=1);


namespace TypeSpecTest;

use Attribute;
use TypeSpec\PropertyInputTypeSpec;
use TypeSpec\InputTypeSpec;
use TypeSpec\ExtractRule\GetStringOrDefault;
use TypeSpec\ProcessRule\ImagickIsRgbColor;

// This InputTypeSpec is repeatable, so that it can be used more
// than once solely for testing purposes. It is not expected for
// people to use Attribute::IS_REPEATABLE normally.
#[Attribute(Attribute::TARGET_PROPERTY|Attribute::IS_REPEATABLE)]
class ImagickColorPropertyInputTypeSpec implements PropertyInputTypeSpec
{
    public function __construct(
        private string $default,
        private string $name
    ) {
    }

    public function getInputTypeSpec(): InputTypeSpec
    {
        return new InputTypeSpec(
            $this->name,
            new GetStringOrDefault($this->default),
            new ImagickIsRgbColor()
        );
    }
}
