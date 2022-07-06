<?php

declare(strict_types = 1);

namespace Type\ProcessRule;

use Type\DataStorage\DataStorage;
use Type\Messages;
use Type\OpenApi\ParamDescription;
use Type\ProcessedValues;
use Type\ValidationResult;

class MaxIntValue implements ProcessPropertyRule
{
    private int $maxValue;

    public function __construct(int $maxValue)
    {
        $this->maxValue = $maxValue;
    }

    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {

//        if (is_int($value) !== true) {
//           // error
//        }
        
        $value = intval($value);
        if ($value > $this->maxValue) {
            $message = sprintf(
                Messages::INT_TOO_LARGE,
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
