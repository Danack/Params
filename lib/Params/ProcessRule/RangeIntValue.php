<?php

declare(strict_types = 1);

namespace Params\ProcessRule;

use Params\InputStorage\InputStorage;
use Params\Messages;
use Params\OpenApi\ParamDescription;
use Params\ProcessedValues;
use Params\ValidationResult;

class RangeIntValue implements ProcessRule
{
    private int $minValue;

    private int $maxValue;

    /**
     *
     * @param int $minValue Value is inclusive
     * @param int $maxValue Value is inclusive
     */
    public function __construct(
        int $minValue,
        int $maxValue
    ) {
        $this->minValue = $minValue;
        $this->maxValue = $maxValue;
    }

    public function process(
        $value,
        ProcessedValues $processedValues,
        InputStorage $inputStorage
    ): ValidationResult {
        $value = intval($value);
        if ($value < $this->minValue) {
            $message = sprintf(
                Messages::INT_TOO_SMALL,
                $this->minValue
            );
            return ValidationResult::errorResult(
                $inputStorage,
                $message
            );
        }

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
        $paramDescription->setMinimum($this->minValue);
        $paramDescription->setExclusiveMinimum(false);

        $paramDescription->setMaximum($this->maxValue);
        $paramDescription->setExclusiveMaximum(false);
    }
}
