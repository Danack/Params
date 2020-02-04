<?php

declare(strict_types=1);

namespace Params\SubsequentRule;

use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ParamsValidator;
use Params\ParamValues;

/**
 * Takes some input and converts it to a bool in a mostly sane way.
 */
class BoolInput implements SubsequentRule
{
    public function process(string $name, $value, ParamValues $validator) : ValidationResult
    {
        if (is_bool($value) === true) {
            return ValidationResult::valueResult($value);
        }

        if (is_string($value) === true) {
            if ($value === 'true' ||
                $value === '1') {
                return ValidationResult::valueResult(true);
            }

            return ValidationResult::valueResult(false);
        }

        if (is_integer($value) === true) {
            if ($value === 0) {
                return ValidationResult::valueResult(false);
            }
            return ValidationResult::valueResult(true);
        }

        if ($value === null) {
            return ValidationResult::valueResult(false);
        }

        $message = sprintf(
            "Unsupported input type of '%s'",
            gettype($value)
        );

        return ValidationResult::errorResult($name, $message);
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        $paramDescription->setType(ParamDescription::FORMAT_BOOLEAN);
    }
}
