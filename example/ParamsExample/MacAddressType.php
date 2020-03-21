<?php

declare(strict_types = 1);

namespace ParamsExample;

use Params\ExtractRule\GetString;
use Params\InputToParamInfo;
use Params\RulesForParamAware;

class MacAddressType implements RulesForParamAware
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
    public static function getInputToParamInfoList(): array
    {
        return [
            new InputToParamInfo(
                'input_name',
                new GetString(),
                new RespectMacRule()
            )
        ];
    }
}
