<?php

declare(strict_types = 1);

namespace Type\ProcessRule;

use Type\DataStorage\DataStorage;
use Type\Messages;
use Type\OpenApi\ParamDescription;
use Type\ProcessedValues;
use Type\ValidationResult;

class MaxFloatValue implements ProcessPropertyRule
{
    public function __construct(private float $maxValue)
    {
    }

    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {

        $value = floatval($value);
        if ($value > $this->maxValue) {
            $message = sprintf(
                Messages::FLOAT_TOO_LARGE,
                $this->maxValue
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
        $paramDescription->setMaximum($this->maxValue);
        $paramDescription->setExclusiveMaximum(false);
    }
}
