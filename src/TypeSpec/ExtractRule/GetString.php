<?php

declare(strict_types=1);

namespace TypeSpec\ExtractRule;

use TypeSpec\DataStorage\DataStorage;
use TypeSpec\Messages;
use TypeSpec\OpenApi\ParamDescription;
use TypeSpec\ProcessedValues;
use TypeSpec\ValidationResult;

class GetString implements ExtractPropertyRule
{
    public function process(
        ProcessedValues $processedValues,
        DataStorage $dataStorage
    ): ValidationResult {
        if ($dataStorage->isValueAvailable() !== true) {
            return ValidationResult::errorResult($dataStorage, Messages::VALUE_NOT_SET);
        }

        $value = $dataStorage->getCurrentValue();

        if (is_array($value) === true) {
            return ValidationResult::errorResult(
                $dataStorage,
                Messages::STRING_REQUIRED_FOUND_NON_SCALAR
            );
        }

        if (is_scalar($value) !== true) {
            return ValidationResult::errorResult(
                $dataStorage,
                Messages::STRING_REQUIRED_FOUND_NON_SCALAR,
            );
        }

        // TODO - reject bools/ints?
        // TODO - needs string input
        $value = (string)$dataStorage->getCurrentValue();

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_STRING);
        $paramDescription->setRequired(true);
    }
}
