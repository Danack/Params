<?php

declare(strict_types=1);

namespace Params\ProcessRule;

use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ParamsValuesImpl;
use Params\ParamValues;
use Params\Path;

class MaxIntValue implements ProcessRule
{
    private int $maxValue;

    public function __construct(int $maxValue)
    {
        $this->maxValue = $maxValue;
    }

    public function process(Path $path, $value, ParamValues $validator) : ValidationResult
    {
        $value = intval($value);
        if ($value > $this->maxValue) {
            return ValidationResult::errorResult(
                $path,
                "Value too large. Max allowed is " . $this->maxValue
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
