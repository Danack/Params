<?php

declare(strict_types=1);

namespace Params\SubsequentRule;

use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ParamsValidator;

class SkipIfNull implements SubsequentRule
{
    public function process(string $name, $value, ParamsValidator $validator) : ValidationResult
    {
        if ($value === null) {
            return ValidationResult::finalValueResult(null);
        }

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        // If we are allowing null, then parameter must be nullable
        // right?
        $paramDescription->setNullAllowed();
    }
}
