<?php

declare(strict_types=1);

namespace Params\Create;

use VarMap\VarMap;
use function Params\createOrError;

trait CreateOrErrorFromVarMap
{
    /**
     * @param VarMap $variableMap
     * @return array<?object, \Params\ValidationProblem[]>
     * @throws \Params\Exception\RulesEmptyException
     * @throws \Params\Exception\ValidationException
     */
    public static function createOrErrorFromVarMap(VarMap $variableMap)
    {
        // TODO - change

        $namedRules = static::getInputParameterList();

        return createOrError(static::class, $namedRules, $variableMap);
    }
}
