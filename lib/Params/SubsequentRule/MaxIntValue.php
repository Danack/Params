<?php

declare(strict_types=1);

namespace Params\SubsequentRule;

use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ParamsValidator;
use Params\ParamValues;

class MaxIntValue implements SubsequentRule
{
    /** @var int  */
    private $maxValue;

    public function __construct(int $maxValue)
    {
        $this->maxValue = $maxValue;
    }

    public function process(string $name, $value, ParamValues $validator) : ValidationResult
    {
        $value = intval($value);
        if ($value > $this->maxValue) {
            return ValidationResult::errorResult(
                $name, "Value too large. Max allowed is " . $this->maxValue
            );
        }

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        $paramDescription->setMaximum($this->maxValue);
        $paramDescription->setExclusiveMaximum(false);
    }
}
