<?php

declare(strict_types=1);

namespace Params\Create;

use Params\ParamsExecutor;
use VarMap\VarMap;

/**
 * Use this trait when the parameters arrive as named parameters e.g
 * either as query string parameters, form elements, or other form body.
 */
trait CreateFromVarMap
{
    /**
     * @param VarMap $variableMap
     * @return self
     * @throws \Params\Exception\RulesEmptyException
     * @throws \Params\Exception\ValidationException
     */
    public static function createFromVarMap(VarMap $variableMap)
    {
        // @TODO - check interface is implemented here.
        if (method_exists(self::class, 'getInputToParamInfoList') === true) {
            $rules = static::getInputToParamInfoList();
        }
        else if (method_exists(self::class, 'getInputParameterList') === true) {
            $rules = static::getInputParameterList();
        }
        else {
            throw new \Exception("Borken.");
        }

        $object = ParamsExecutor::create(static::class, $rules, $variableMap);
        /** @var $object self */
        return $object;
    }
}
