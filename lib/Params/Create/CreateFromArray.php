<?php

declare(strict_types=1);

namespace Params\Create;

use Params\DataStorage\ArrayDataStorage;
use VarMap\ArrayVarMap;
use VarMap\VarMap;
use function Params\create;

/**
 * Use this trait when the parameters arrive as named parameters e.g
 * either as query string parameters, form elements, or other form body.
 */
trait CreateFromArray
{
    /**
     * @param VarMap $variableMap
     * @return self
     * @throws \Params\Exception\ValidationException
     */
    public static function createFromArray($data)
    {
        $rules = static::getInputParameterList();

        $dataStorage = ArrayDataStorage::fromArray($data);

//        $variableMap = new ArrayVarMap($data);
        $object = create(
            static::class,
            $rules,
            $dataStorage
        );

        /** @var $object self */
        return $object;
    }
}
