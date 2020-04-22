<?php

declare(strict_types=1);

namespace Params\Create;

use Params\DataLocator\DataStorage;
use Params\Exception\ValidationException;
use Params\ExtractRule\GetType;
use VarMap\VarMap;
use function Params\createArrayOfType;

/**
 * Use this trait when the parameters arrive as named parameters e.g
 * either as query string parameters, form elements, or other form body.
 */
trait CreateArrayOfTypeFromArray
{
    /**
     * @param VarMap $variableMap
     * @return self[]
     * @throws \Params\Exception\ValidationException
     */
    public static function createArrayOfTypeFromArray(array $data)
    {
        $getType = GetType::fromClass(self::class);
        $dataStorage = DataStorage::fromArray($data);

        $validationResult = createArrayOfType(
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
