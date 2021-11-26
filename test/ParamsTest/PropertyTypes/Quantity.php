<?php

namespace ParamsTest\PropertyTypes;

use Params\ExtractRule\GetInt;
use Params\InputParameter;
use Params\Param;
use Params\ProcessRule\MaxIntValue;
use Params\ProcessRule\MinIntValue;

#[\Attribute]
class Quantity implements Param
{
    public function __construct(
        private string $name
    ) {
    }

    public function getInputParameter(): InputParameter
    {
        return new InputParameter(
            $this->name,
            new GetInt(),
            new MinIntValue(1),
            new MaxIntValue(20),
        );
    }
}
