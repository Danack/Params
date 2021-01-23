<?php

declare(strict_types=1);

namespace Params\ExtractRule;

use Params\InputStorage\InputStorage;
use Params\Messages;
use Params\OpenApi\ParamDescription;
use Params\ProcessedValues;
use Params\ValidationResult;

class GetString implements ExtractRule
{
    public function process(
        ProcessedValues $processedValues,
        InputStorage $dataLocator
    ): ValidationResult {
        if ($dataLocator->isValueAvailable() !== true) {
            return ValidationResult::errorResult($dataLocator, Messages::VALUE_NOT_SET);
        }

        $value = $dataLocator->getCurrentValue();

        if (is_array($value) === true) {
            return ValidationResult::errorResult($dataLocator, Messages::STRING_REQUIRED_FOUND_NON_SCALAR);
        }

        if (is_scalar($value) !== true) {
            return ValidationResult::errorResult(
                $dataLocator,
                Messages::STRING_REQUIRED_FOUND_NON_SCALAR,
            );
        }

        // TODO - reject bools/ints?
        // TODO - needs string input
        $value = (string)$dataLocator->getCurrentValue();

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_STRING);
        $paramDescription->setRequired(true);
    }
}
