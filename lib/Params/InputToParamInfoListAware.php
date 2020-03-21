<?php

declare(strict_types = 1);

namespace Params;

/**
 * Defines the
 */
interface InputToParamInfoListAware
{
    /**
     * @return \Params\InputToParamInfo[]
     */
    public static function getInputToParamInfoList();
}
