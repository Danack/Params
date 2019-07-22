<?php

declare(strict_types=1);

namespace Params\SubsequentRule;

use Params\Functions;
use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ParamsValidator;
use Params\ParamValues;

/**
 * Class PositiveIntValidator
 *
 * Checks an input is above zero and a sane int.
 */
class PositiveInt implements SubsequentRule
{
    const MAX_SANE_VALUE = 1024 * 1024 * 1024 * 1024;

    public function process(string $name, $value, ParamValues $validator) : ValidationResult
    {
        $matches = null;

        $errorMessage = Functions::check_only_digits($name, $value);
        if ($errorMessage !== null) {
            return ValidationResult::errorResult($name, $errorMessage);
        }

        $value = intval($value);
        $maxValue = self::MAX_SANE_VALUE;
        if ($value > $maxValue) {
            return ValidationResult::errorResult($name, "Value too large. Max allowed is $maxValue");
        }

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        $paramDescription->setType(ParamDescription::TYPE_INTEGER);
        $paramDescription->setMinimum(0);
        $paramDescription->setExclusiveMinimum(false);
    }
}
