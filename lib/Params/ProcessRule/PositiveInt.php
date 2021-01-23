<?php

declare(strict_types = 1);

namespace Params\ProcessRule;

use Params\InputStorage\InputStorage;
use Params\OpenApi\ParamDescription;
use Params\ProcessedValues;
use Params\ValidationResult;
use Params\Messages;
use function Params\check_only_digits;

/**
 * Class PositiveIntValidator
 *
 * Checks an input is above zero and a sane int.
 */
class PositiveInt implements ProcessRule
{
    const MAX_SANE_VALUE = 1_024 * 1_024 * 1_024 * 1_024;

    public function process(
        $value,
        ProcessedValues $processedValues,
        InputStorage $inputStorage
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
