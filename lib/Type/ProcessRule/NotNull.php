<?php

declare(strict_types = 1);

namespace Type\ProcessRule;

use Type\DataStorage\DataStorage;
use Type\Messages;
use Type\OpenApi\ParamDescription;
use Type\ProcessedValues;
use Type\ValidationResult;

/**
 * @TODO - is there any point to this rule?
 */
class NotNull implements ProcessPropertyRule
{
    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {
        if ($value === null) {
            return ValidationResult::errorResult(
                $inputStorage,
                Messages::NULL_NOT_ALLOWED
            );
        }
        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setNullAllowed(false);
    }
}
