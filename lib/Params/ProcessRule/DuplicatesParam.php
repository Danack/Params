<?php

declare(strict_types = 1);

namespace Params\ProcessRule;

use Params\InputStorage\InputStorage;
use Params\Messages;
use Params\OpenApi\ParamDescription;
use Params\ProcessedValues;
use Params\ValidationResult;

/**
 *
 */
class DuplicatesParam implements ProcessRule
{
    private string $paramToDuplicate;

    /**
     * @param string $paramToDuplicate The name of the param this one should be the same as.
     */
    public function __construct(string $paramToDuplicate)
    {
        $this->paramToDuplicate = $paramToDuplicate;
    }


    public function process(
        $value,
        ProcessedValues $processedValues,
        InputStorage $inputStorage
    ): ValidationResult {
        if ($processedValues->hasValue($this->paramToDuplicate) !== true) {
            $message = sprintf(
                Messages::ERROR_NO_PREVIOUS_PARAM,
                $this->paramToDuplicate
            );

            return ValidationResult::errorResult($inputStorage, $message);
        }

        $previousValue = $processedValues->getValue($this->paramToDuplicate);

        $previousType = gettype($previousValue);
        $currentType = gettype($value);

        if ($previousType !== $currentType) {
            $message = sprintf(
                Messages::ERROR_DIFFERENT_TYPES,
                $this->paramToDuplicate,
                $previousType,
                $currentType
            );

            return ValidationResult::errorResult($inputStorage, $message);
        }

        if ($value !== $previousValue) {
            $message = sprintf(
                Messages::ERROR_DIFFERENT_VALUE,
                $this->paramToDuplicate
            );
            return ValidationResult::errorResult($inputStorage, $message);
        }

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $message = sprintf(
            Messages::MUST_DUPLICATE_PARAMETER,
            $this->paramToDuplicate
        );

        $paramDescription->setDescription($message);
    }
}
