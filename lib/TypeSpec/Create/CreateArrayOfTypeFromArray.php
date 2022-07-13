<?php

declare(strict_types=1);

namespace TypeSpec\Create;

use TypeSpec\DataStorage\ArrayDataStorage;
use TypeSpec\Exception\ValidationException;
use TypeSpec\ExtractRule\GetType;
use VarMap\VarMap;
use function TypeSpec\createArrayOfTypeFromInputStorage;

/**
 * Use this trait when the parameters arrive as named parameters e.g
 * either as query string parameters, form elements, or other form body.
 */
trait CreateArrayOfTypeFromArray
{
    /**
     * @param VarMap $variableMap
     * @return self[]
     * @throws \TypeSpec\Exception\ValidationException
     */
    public static function createArrayOfTypeFromArray(array $data)
    {
        $getType = GetType::fromClass(self::class);
        $dataStorage = ArrayDataStorage::fromArray($data);

        $validationResult = createArrayOfTypeFromInputStorage(
            $dataStorage,
            $getType
        );

        if ($validationResult->anyErrorsFound() === true) {
            throw new ValidationException(
                "Validation problems",
                $validationResult->getValidationProblems()
            );
        }

        $objects = $validationResult->getValue();

        /** @var self[] self */
        return $objects;
    }
}
