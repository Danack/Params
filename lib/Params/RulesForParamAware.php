<?php

declare(strict_types = 1);

namespace Params;

interface RulesForParamAware
{
    /**
     * @return \Params\Param[]
     */
    public static function getInputToParamInfoList(): array;
}
