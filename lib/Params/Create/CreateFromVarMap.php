<?php

declare(strict_types=1);

namespace Params\Create;

use Params\DataLocator\DataStorage;
use VarMap\VarMap;
use function Params\create;

/**
 * Use this trait when the parameters arrive as named parameters e.g
 * either as query string parameters, form elements, or other form body.
 */
trait CreateFromVarMap
{
    /**
     * @param VarMap $variableMap
     * @return self
     * @throws \Params\Exception\RulesEmptyException
     * @throws \Params\Exception\ValidationException
     */
    public static function createFromVarMap(VarMap $variableMap)
    {
        // @TODO - check interface is implemented here.
        if (method_exists(self::class, 'getInputParameterList') === true) {
            $rules = static::getInputParameterList();
        }
        else {
            throw new \Exception("Borken.");
        }

        $dataLocator = DataStorage::fromVarMap($variableMap);

        $object = create(static::class, $rules, $variableMap, $dataLocator);
        /** @var $object self */
        return $object;
    }
}
