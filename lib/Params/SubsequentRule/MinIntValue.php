<?php

declare(strict_types=1);

namespace Params\SubsequentRule;

use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ParamsValidator;

class MinIntValue implements SubsequentRule
{
    /** @var int  */
    private $minValue;

    public function __construct(int $minValue)
    {
        $this->minValue = $minValue;
    }

    public function process(string $name, $value, ParamsValidator $validator) : ValidationResult
    {
        $value = intval($value);
        if ($value < $this->minValue) {
            return ValidationResult::errorResult("Value too small. Min allowed is " . $this->minValue);
        }

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        $paramDescription->setMinimum($this->minValue);
        $paramDescription->setExclusiveMinimum(false);
    }
}
