<?php


namespace ParamsTest\PropertyTypes;

use Params\ExtractRule\GetArrayOfParam;
use Params\InputParameter;
use Params\Param;
//use ParamsTest\DTOTypes\BasicDTO;
use ParamsTest\PropertyTypes\Quantity;

#[\Attribute]
class MultipleBasicArray implements Param
{
    public function __construct(
        private string $name
    ) {
    }

    public function getInputParameter(): InputParameter
    {
        return new InputParameter(
            $this->name,
            new GetArrayOfParam(Quantity::class),
        );
    }
}
