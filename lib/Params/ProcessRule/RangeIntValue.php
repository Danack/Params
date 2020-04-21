<?php

declare(strict_types = 1);

namespace Params\ProcessRule;

use Params\DataLocator\InputStorageAye;
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
        InputStorageAye $dataLocator
    ): ValidationResult {
        $value = intval($value);
        if ($value < $this->minValue) {
            return ValidationResult::errorResult(
                $dataLocator,
                "Value too small. Min allowed is " . $this->minValue
            );
        }

        if ($value > $this->maxValue) {
            return ValidationResult::errorResult(
                $dataLocator,
                "Value too large. Max allowed is " . $this->maxValue
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
