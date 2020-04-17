<?php

declare(strict_types=1);

namespace Params\Create;

use Params\DataLocator\InputStorageAye;
use Params\Exception\InputParameterListException;
use Params\Exception\ValidationException;
use Params\InputParameter;
use Params\Path;
use VarMap\VarMap;
use function Params\createArrayForTypeWithRules;
use Params\DataLocator\DataStorage;
use function Params\createArrayOfTypeDja;
use Params\ExtractRule\GetType;

/**
 * Use this trait when the parameters arrive as named parameters e.g
 * either as query string parameters, form elements, or other form body.
 */
trait CreateArrayOfTypeFromArray
{
    /**
     * @param VarMap $variableMap
     * @return self[]
     * @throws \Params\Exception\RulesEmptyException
     * @throws \Params\Exception\ValidationException
     */
    public static function createArrayOfTypeFromArray(array $data)
    {
        $getType = GetType::fromClass(self::class);
        $dataStorage = DataStorage::fromArray($data);

        $validationResult = createArrayOfTypeDja(
            $dataStorage,
            $data,
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
