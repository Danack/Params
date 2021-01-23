<?php

declare(strict_types = 1);

namespace Params\ProcessRule;

use Params\InputStorage\InputStorage;
use Params\OpenApi\ParamDescription;
use Params\ProcessedValues;
use Params\ValidationResult;

/**
 * Convert the value to null if the string is empty, and provides
 * a final result
 */
class NullIfEmpty implements ProcessRule
{
    public function process(
        $value,
        ProcessedValues $processedValues,
        InputStorage $inputStorage
    ): ValidationResult {
        if ($value === null) {
            return ValidationResult::finalValueResult(null);
        }

        $temp_value = (string)$value;
        $temp_value = trim($temp_value);

        if (strlen($temp_value) === 0) {
            return ValidationResult::finalValueResult(null);
        }

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        // If we are allowing null, then parameter
        // must be nullable, right?
        $paramDescription->setNullAllowed(true);
    }
}
