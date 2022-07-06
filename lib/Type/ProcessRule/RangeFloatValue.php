<?php

declare(strict_types = 1);

namespace Type\ProcessRule;

use Type\DataStorage\DataStorage;
use Type\Messages;
use Type\OpenApi\ParamDescription;
use Type\ProcessedValues;
use Type\ValidationResult;

class RangeFloatValue implements ProcessPropertyRule
{
    /**
     *
     * @param float $minValue Value is inclusive
     * @param float $maxValue Value is inclusive
     */
    public function __construct(
        private float $minValue,
        private float $maxValue
    ) {
    }

    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {
        $value = floatval($value);
        if ($value < $this->minValue) {
            $message = sprintf(
                Messages::FLOAT_TOO_SMALL,
                $this->minValue
            );
            return ValidationResult::errorResult(
                $inputStorage,
                $message
            );
        }

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
        $paramDescription->setMinimum($this->minValue);
        $paramDescription->setExclusiveMinimum(false);

        $paramDescription->setMaximum($this->maxValue);
        $paramDescription->setExclusiveMaximum(false);
    }
}
