<?php

declare(strict_types=1);

namespace Type\Create;

use Type\DataStorage\ArrayDataStorage;
use VarMap\ArrayVarMap;
use VarMap\VarMap;
use function Type\create;
use function Type\getPropertyDefinitionsForClass;

/**
 * Use this trait when the parameters arrive as named parameters e.g
 * either as query string parameters, form elements, or other form body.
 */
trait CreateFromArray
{
    /**
     * @param VarMap $variableMap
     * @return self
     * @throws \Type\Exception\ValidationException
     */
    public static function createFromArray($data)
    {
        $rules = getPropertyDefinitionsForClass(self::class);

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
