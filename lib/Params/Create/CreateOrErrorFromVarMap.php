<?php

declare(strict_types=1);

namespace Params\Create;

use Params\Params;
use VarMap\VarMap;

trait CreateOrErrorFromVarMap
{
    /**
     * @param VarMap $variableMap
     * @return array{0:object|null, 1:\Params\ValidationErrors|null}
     * @throws \Params\Exception\RulesEmptyException
     * @throws \Params\Exception\ValidationException
     */
    public static function createOrErrorFromVarMap(VarMap $variableMap)
    {
        $namedRules = static::getInputToParamInfoList();

        return Params::createOrError(static::class, $namedRules, $variableMap);
    }
}
