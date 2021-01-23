<?php

declare(strict_types=1);

namespace Params\ExtractRule;

use Params\InputStorage\InputStorage;
use Params\OpenApi\ParamDescription;
use Params\ProcessedValues;
use Params\ProcessRule\BoolInput;
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
        InputStorage $dataLocator
    ): ValidationResult {

        if ($dataLocator->isValueAvailable() !== true) {
            return ValidationResult::valueResult(null);
        }

        $intRule = new BoolInput();
        return $intRule->process(
            $dataLocator->getCurrentValue(),
            $processedValues,
            $dataLocator
        );
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_BOOLEAN);
        $paramDescription->setRequired(false);
    }
}
