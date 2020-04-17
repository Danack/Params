<?php

declare(strict_types = 1);


namespace Params;

interface PatchInputParameterList
{
    /**
     * @return \Params\PatchInputParameter[]
     */
    public static function getPatchInputParameterList();
}
