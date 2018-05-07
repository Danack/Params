<?php

declare(strict_types=1);

namespace Params\Rule;

use Params\Functions;
use Params\Rule;
use Params\ValidationResult;

/**
 * Class PositiveIntValidator
 *
 * Checks an input is above zero and a sane int.
 */
class PositiveInt implements Rule
{
    const MAX_SANE_VALUE = 1024 * 1024 * 1024 * 1024;

    public function __invoke(string $name, $value): ValidationResult
    {
        $matches = null;

        $errorMessage = Functions::check_only_digits($name, $value);
        if ($errorMessage !== null) {
            return ValidationResult::errorResult($errorMessage);
        }

        $value = intval($value);
        $maxValue = self::MAX_SANE_VALUE;
        if ($value > $maxValue) {
            return ValidationResult::errorResult("Value too large. Max allowed is $maxValue");
        }

        return ValidationResult::valueResult($value);
    }
}
