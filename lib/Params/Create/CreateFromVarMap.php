<?php

declare(strict_types=1);

namespace Params\Create;

use Params\DataLocator\DataStorage;
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

        $dataLocator = DataStorage::fromVarMap($variableMap);

        $object = create(static::class, $rules, $dataLocator);
        /** @var $object self */
        return $object;
    }
}
