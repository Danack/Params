<?php


namespace ParamsTest\PropertyTypes;

use Params\ExtractRule\GetStringOrDefault;
use Params\ProcessRule\Enum;
use Params\InputParameter;
use Params\Param;

#[\Attribute]
class KnownColors implements Param
{
    const KNOWN_COLORS = ['red', 'green', 'blue'];

    public function __construct(
        private string $name
    ) {
    }

    public function getInputParameter(): InputParameter
    {
        return new InputParameter(
            $this->name,
            new GetStringOrDefault('blue'),
            new Enum(self::KNOWN_COLORS)
        );
    }
}
