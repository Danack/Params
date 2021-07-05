<?php

declare(strict_types = 1);

namespace Params;

trait InputParameterListFromAttributes
{
    /**
     * @return \Params\InputParameter[]
     */
    public static function getInputParameterList(): array
    {
        return getParamsFromAnnotations(get_called_class());
    }
}
