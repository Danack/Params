<?php

declare(strict_types = 1);

namespace Type\ProcessRule;

use Type\DataStorage\DataStorage;
use Type\OpenApi\ParamDescription;
use Type\ProcessedValues;
use Type\ValidationResult;
use Type\Messages;
use function Type\check_only_digits;

/**
 * Class PositiveIntValidator
 *
 * Checks an input is above zero and a sane int.
 */
class PositiveInt implements ProcessPropertyRule
{
    const MAX_SANE_VALUE = 1_024 * 1_024 * 1_024 * 1_024;

    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {
        $matches = null;

        $errorMessage = check_only_digits($value);
        if ($errorMessage !== null) {
            return ValidationResult::errorResult($inputStorage, $errorMessage);
        }

        $value = intval($value);
        $maxValue = self::MAX_SANE_VALUE;
        if ($value > $maxValue) {
            $message = sprintf(
                Messages::INT_OVER_LIMIT,
                $maxValue
            );

            return ValidationResult::errorResult(
                $inputStorage,
                $message
            );
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
