<?php

declare(strict_types=1);

namespace Aitekz\Params;

use Params\Params;
use VarMap\VarMap;

trait CreateFromVarMap
{
    /**
     * @param VarMap $variableMap
     * @return object|static
     * @throws \Params\Exception\RulesEmptyException
     * @throws \Params\Exception\ValidationException
     */
    public static function create(VarMap $variableMap)
    {
        $rules = static::getRules($variableMap);
        return Params::create(static::class, $rules);
    }
}
