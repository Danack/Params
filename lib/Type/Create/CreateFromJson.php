<?php

declare(strict_types=1);

namespace Type\Create;

use Type\DataStorage\ArrayDataStorage;
use VarMap\ArrayVarMap;
use VarMap\VarMap;
use function Type\create;
use function JsonSafe\json_decode_safe;
use function Type\getPropertyDefinitionsForClass;

/**
 * Use this trait when the parameters arrive as named parameters e.g
 * either as query string parameters, form elements, or other form body.
 */
trait CreateFromJson
{
    /**
     * @param string $json
     * @return self
     * @throws \Type\Exception\ValidationException
     */
    public static function createFromJson($json)
    {
        $rules = getPropertyDefinitionsForClass(self::class);
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
