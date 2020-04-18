<?php

declare(strict_types=1);

namespace Params\Create;

use Params\DataLocator\DataStorage;
use VarMap\VarMap;
use function Params\createOrError;

trait CreateOrErrorFromVarMap
{
    /**
     * @param VarMap $variableMap
     * @return array<?object, \Params\ValidationProblem[]>
     * @throws \Params\Exception\ValidationException
     */
    public static function createOrErrorFromVarMap(VarMap $variableMap)
    {
        $namedRules = static::getInputParameterList();
        $dataLocator = DataStorage::fromVarMap($variableMap);

        return createOrError(static::class, $namedRules, $dataLocator);
    }
}
