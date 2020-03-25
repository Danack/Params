<?php

declare(strict_types=1);

namespace Params\ProcessRule;

use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ParamsValuesImpl;
use Params\ParamValues;
use Params\Path;

/**
 * Convert the value to null if the string is empty, and provides
 * a final result
 */
class NullIfEmpty implements ProcessRule
{
    public function process(Path $path, $value, ParamValues $validator) : ValidationResult
    {
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
        // If we are allowing null, then parameter must be nullable
        // right?
        $paramDescription->setNullAllowed();
    }
}
