<?php

declare(strict_types=1);

namespace Params\ProcessRule;

use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ParamsValuesImpl;
use Params\ParamValues;
use Params\Path;

class Trim implements ProcessRule
{
    public function process(Path $path, $value, ParamValues $validator) : ValidationResult
    {
        return ValidationResult::valueResult(trim($value));
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        // Does nothing?
    }
}
