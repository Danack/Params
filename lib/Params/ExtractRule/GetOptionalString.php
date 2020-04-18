<?php

declare(strict_types=1);

namespace Params\ExtractRule;

use Params\DataLocator\InputStorageAye;
use Params\OpenApi\ParamDescription;
use Params\ProcessedValues;
use Params\ValidationResult;

class GetOptionalString implements ExtractRule
{
    public function process(
        ProcessedValues $processedValues,
        InputStorageAye $dataLocator
    ): ValidationResult {
        if ($dataLocator->valueAvailable() !== true) {
            return ValidationResult::valueResult(null);
        }

        // TODO - convert strings better.
        $value = (string)$dataLocator->getCurrentValue();

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_STRING);
        $paramDescription->setRequired(false);
    }
}
