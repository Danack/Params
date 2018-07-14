<?php

declare(strict_types=1);

namespace Params;

use VarMap\VarMap;

/**
 * Use this trait when the parameters arrive as named parameters e.g
 * either as query string parameters, form elements, or other form body.
 */
trait CreateFromVarMap
{
    /**
     * @param VarMap $variableMap
     * @return object|static
     * @throws \Params\Exception\RulesEmptyException
     * @throws \Params\Exception\ValidationException
     */
    public static function createFromVarMap(VarMap $variableMap)
    {
        $rules = static::getRules($variableMap);
        return Params::create(static::class, $rules);
    }
}