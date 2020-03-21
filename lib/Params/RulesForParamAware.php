<?php

declare(strict_types = 1);

namespace Params;

interface RulesForParamAware
{
    /**
     * @return \Params\InputToParamInfo[]
     */
    public static function getInputToParamInfoList(): array;
}
