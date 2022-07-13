<?php

declare(strict_types=1);

namespace TypeSpec\Create;

use TypeSpec\DataStorage\ArrayDataStorage;
use VarMap\ArrayVarMap;
use VarMap\VarMap;
use function TypeSpec\create;
use function JsonSafe\json_decode_safe;
use function TypeSpec\getInputTypeSpecListForClass;

/**
 * Use this trait when the parameters arrive as named parameters e.g
 * either as query string parameters, form elements, or other form body.
 */
trait CreateFromJson
{
    /**
     * @param string $json
     * @return self
     * @throws \TypeSpec\Exception\ValidationException
     */
    public static function createFromJson($json)
    {
        $rules = getInputTypeSpecListForClass(self::class);
        $data = json_decode_safe($json);
        $dataStorage = ArrayDataStorage::fromArray($data);

        $variableMap = new ArrayVarMap($data);
        $object = create(
            static::class,
            $rules,
            $dataStorage
        );

        /** @var $object self */
        return $object;
    }
}
