<?php

declare(strict_types=1);

namespace Params\Create;

use Params\Params;
use VarMap\ArrayVarMap;
use VarMap\VarMap;

/**
 * Use this trait when the parameters arrive as named parameters e.g
 * either as query string parameters, form elements, or other form body.
 */
trait CreateFromArray
{
    /**
     * @param VarMap $variableMap
     * @return self
     * @throws \Params\Exception\RulesEmptyException
     * @throws \Params\Exception\ValidationException
     */
    public static function createFromArray($data)
    {
        $rules = static::getInputToParamInfoList();

        $variableMap = new ArrayVarMap($data);
        $object = Params::create(static::class, $rules, $variableMap);
        /** @var $object self */
        return $object;
    }
}
