<?php

declare(strict_types = 1);

namespace ParamsExample;

use Params\ExtractRule\GetString;
use Params\InputParameter;

class MacAddressType
{
    /** @var string */
    private $value;

    /**
     *
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    public static function getParamInfo(string $inputName): InputParameter
    {
        return new InputParameter(
            $inputName,
            new GetString(),
            new RespectMacRule()
        );
    }
}
