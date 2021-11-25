<?php


namespace ParamsExample\PropertyTypes;

use Params\ExtractRule\GetStringOrDefault;
use Params\ProcessRule\Enum;
use Params\InputParameter;
use Params\Param;

#[\Attribute]
class KnownColors implements Param
{
    public function __construct(
        private string $name
    ) {
    }

    public function getInputParameter(): InputParameter
    {
        return new InputParameter(
            $this->name,
            new GetStringOrDefault('blue'),
            new Enum(['red', 'green', 'blue'])
        );
    }
}
