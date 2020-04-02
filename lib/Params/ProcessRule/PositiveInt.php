<?php

declare(strict_types=1);

namespace Params\ProcessRule;

use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ParamValues;
use Params\Path;
use function Params\check_only_digits;

/**
 * Class PositiveIntValidator
 *
 * Checks an input is above zero and a sane int.
 */
class PositiveInt implements ProcessRule
{
    const MAX_SANE_VALUE = 1_024 * 1_024 * 1_024 * 1_024;

    public function process(Path $path, $value, ParamValues $validator) : ValidationResult
    {
        $matches = null;

        $errorMessage = check_only_digits($path->toString(), $value);
        if ($errorMessage !== null) {
            return ValidationResult::errorResult($path, $errorMessage);
        }

        $value = intval($value);
        $maxValue = self::MAX_SANE_VALUE;
        if ($value > $maxValue) {
            return ValidationResult::errorResult($path, "Value too large. Max allowed is $maxValue");
        }

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_INTEGER);
        $paramDescription->setMinimum(0);
        $paramDescription->setExclusiveMinimum(false);
    }
}
