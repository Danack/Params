<?php

declare(strict_types=1);

namespace Type\Create;

use Type\DataStorage\ArrayDataStorage;
use VarMap\VarMap;
use function Type\createOrError;
use function Type\getPropertyDefinitionsForClass;

trait CreateOrErrorFromVarMap
{
    /**
     * @param VarMap $variableMap
     * @return array<?object, \Type\ValidationProblem[]>
     * @throws \Type\Exception\ValidationException
     */
    public static function createOrErrorFromVarMap(VarMap $variableMap)
    {
        $rules = getPropertyDefinitionsForClass(self::class);
        $dataStorage = ArrayDataStorage::fromArray($variableMap->toArray());

        return createOrError(static::class, $rules, $dataStorage);
    }
}
