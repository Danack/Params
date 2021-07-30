<?php

declare(strict_types=1);

namespace Params\ExtractRule;

use Params\DataStorage\DataStorage;
use Params\OpenApi\ParamDescription;
use Params\ProcessedValues;
use Params\ProcessRule\CastToBool;
use Params\ValidationResult;

/**
 *
 * If a parameter is not set, then the value is null, otherwise
 * it must be a valid integer.
 *
 */
class GetOptionalBool implements ExtractRule
{
    public function process(
        ProcessedValues $processedValues,
        DataStorage $dataStorage
    ): ValidationResult {

        if ($dataStorage->isValueAvailable() !== true) {
            return ValidationResult::valueResult(null);
        }

        $intRule = new CastToBool();
        return $intRule->process(
            $dataStorage->getCurrentValue(),
            $processedValues,
            $dataStorage
        );
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_BOOLEAN);
        $paramDescription->setRequired(false);
    }
}
