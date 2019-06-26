<?php

declare(strict_types=1);

namespace Params\SubsequentRule;

use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ParamsValidator;

class Trim implements SubsequentRule
{
    public function process(string $name, $value, ParamsValidator $validator) : ValidationResult
    {
        return ValidationResult::valueResult(trim($value));
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        // Does nothing?
    }
}
