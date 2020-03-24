<?php

declare(strict_types=1);

namespace Params\ProcessRule;

use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ParamsValuesImpl;
use Params\ParamValues;

class SkipIfNull implements ProcessRule
{
    public function process(string $name, $value, ParamValues $validator) : ValidationResult
    {
        if ($value === null) {
            return ValidationResult::finalValueResult(null);
        }

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        // If we are allowing null, then parameter must be nullable
        // right?
        $paramDescription->setNullAllowed();
    }
}
