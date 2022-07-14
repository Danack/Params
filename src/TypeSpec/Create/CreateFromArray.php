<?php

declare(strict_types=1);

namespace TypeSpec\Create;

use TypeSpec\DataStorage\ArrayDataStorage;
use VarMap\VarMap;
use function TypeSpec\create;
use function TypeSpec\getInputTypeSpecListForClass;

/**
 * Use this trait when the parameters arrive as named parameters e.g
 * either as query string parameters, form elements, or other form body.
 */
trait CreateFromArray
{
    /**
     * @param VarMap $variableMap
     * @return self
     * @throws \TypeSpec\Exception\ValidationException
     */
    public static function createFromArray($data)
    {
        $rules = getInputTypeSpecListForClass(self::class);

        $dataStorage = ArrayDataStorage::fromArray($data);

        $object = create(
            static::class,
            $rules,
            $dataStorage
        );

        /** @var $object self */
        return $object;
    }
}
