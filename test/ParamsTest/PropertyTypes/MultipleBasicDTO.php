<?php


namespace ParamsTest\PropertyTypes;

use Params\ExtractRule\GetArrayOfType;
use Params\InputParameter;
use Params\Param;
use ParamsTest\DTOTypes\BasicDTO;

#[\Attribute]
class MultipleBasicDTO implements Param
{
    public function __construct(
        private string $name
    ) {
    }

    public function getInputParameter(): InputParameter
    {
        return new InputParameter(
            $this->name,
            new GetArrayOfType(BasicDTO::class),
        );
    }
}
