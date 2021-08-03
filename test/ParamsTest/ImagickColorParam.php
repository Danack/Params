<?php

declare(strict_types=1);


namespace ParamsTest;

use Attribute;
use Params\Param;
use Params\InputParameter;
use Params\ExtractRule\GetStringOrDefault;
use Params\ProcessRule\ImagickIsRgbColor;

#[Attribute(Attribute::TARGET_PROPERTY|Attribute::IS_REPEATABLE)]
class ImagickColorParam implements Param
{
    public function __construct(
        private string $default,
        private string $name
    ) {
    }

    public function getInputParameter(): InputParameter
    {
        return new InputParameter(
            $this->name,
            new GetStringOrDefault($this->default),
            new ImagickIsRgbColor()
        );
    }
}
