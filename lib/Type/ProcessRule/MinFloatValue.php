<?php

declare(strict_types = 1);

namespace Type\ProcessRule;

use Type\DataStorage\DataStorage;
use Type\OpenApi\ParamDescription;
use Type\ProcessedValues;
use Type\ValidationResult;
use Type\Messages;

class MinFloatValue implements ProcessPropertyRule
{


    public function __construct(private float $minValue)
    {
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

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setMinimum($this->minValue);
        $paramDescription->setExclusiveMinimum(false);
    }
}
