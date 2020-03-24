<?php

declare(strict_types=1);

namespace Params\ProcessRule;

use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ParamsValuesImpl;
use Params\ParamValues;

class MinIntValue implements ProcessRule
{
    private int $minValue;

    public function __construct(int $minValue)
    {
        $this->minValue = $minValue;
    }

    public function process(string $name, $value, ParamValues $validator) : ValidationResult
    {
        $value = intval($value);
        if ($value < $this->minValue) {
            return ValidationResult::errorResult(
                $name,
                "Value too small. Min allowed is " . $this->minValue
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
