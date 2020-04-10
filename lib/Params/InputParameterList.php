<?php

declare(strict_types = 1);


namespace Params;

interface InputParameterList
{
    /**
     * @return \Params\InputParameter[]
     */
    public static function getInputParameterList();
}
