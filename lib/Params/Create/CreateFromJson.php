<?php

declare(strict_types=1);

namespace Params\Create;

use Params\DataLocator\DataStorage;
use VarMap\ArrayVarMap;
use VarMap\VarMap;
use function Params\create;
use function JsonSafe\json_decode_safe;

/**
 * Use this trait when the parameters arrive as named parameters e.g
 * either as query string parameters, form elements, or other form body.
 */
trait CreateFromJson
{
    /**
     * @param string $json
     * @return self
     * @throws \Params\Exception\ValidationException
     */
    public static function createFromJson($json)
    {
        $rules = static::getInputParameterList();
        $data = json_decode_safe($json);
        $dataLocator = DataStorage::fromArray($data);

        $variableMap = new ArrayVarMap($data);
        $object = create(
            static::class,
            $rules,
            $dataLocator
        );

        /** @var $object self */
        return $object;
    }
}
