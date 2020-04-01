<?php

declare(strict_types = 1);

namespace ParamsExample;

use Params\ExtractRule\GetString;
use Params\Param;
use Params\ParamAware;

class MacAddressType implements ParamAware
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

    public static function getParamInfo(string $inputName): Param
    {
        return new Param(
            $inputName,
            new GetString(),
            new RespectMacRule()
        );
    }
}
