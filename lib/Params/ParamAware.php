<?php

declare(strict_types = 1);

namespace Params;

/**
 * Allows you to retrieve the param rules for a specific type
 */
interface ParamAware
{
    /**
     * @return \Params\Param
     */
    public static function getParamInfo(string $inputName): Param;
}
