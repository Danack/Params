<?php

declare(strict_types = 1);


namespace Params;

interface InputParameterList
{
    /**
     * @return \Params\Param[]
     */
    public static function getInputParameterList();
}
