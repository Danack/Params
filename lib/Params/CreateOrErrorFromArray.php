<?php

declare(strict_types=1);

namespace Params;

use VarMap\ArrayVarMap;
use VarMap\VarMap;

trait CreateOrErrorFromArray
{
    /**
     * @param VarMap $variableMap
     * @return array{0:self|null, 1:\Params\ValidationErrors|null}
     * @throws \Params\Exception\RulesEmptyException
     * @throws \Params\Exception\ValidationException
     */
    public static function createOrErrorFromArray(array $data)
    {
        $variableMap = new ArrayVarMap($data);

        $namedRules = static::getRules($variableMap);

        return Params::createOrError(static::class, $namedRules);
    }
}
