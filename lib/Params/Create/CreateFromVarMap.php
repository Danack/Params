<?php

declare(strict_types=1);

namespace Params\Create;

use Params\DataStorage\ArrayDataStorage;
use VarMap\VarMap;
use function Params\create;
use function Params\getInputParameterListForClass;

/**
 * Use this trait when the parameters arrive as named parameters e.g
 * either as query string parameters, form elements, or other form body.
 */
trait CreateFromVarMap
{
    /**
     * @param VarMap $variableMap
     * @return self
     * @throws \Params\Exception\ValidationException
     */
    public static function createFromVarMap(VarMap $variableMap)
    {
        $rules = getInputParameterListForClass(self::class);

        $dataStorage = ArrayDataStorage::fromArray($variableMap->toArray());

        $object = create(static::class, $rules, $dataStorage);
        /** @var $object self */
        return $object;
    }
}
