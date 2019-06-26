<?php

declare(strict_types=1);

namespace Params\SubsequentRule;

use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ParamsValidator;

/**
 * @TODO - is there any point to this rule?
 */
class NotNull implements SubsequentRule
{
    public function process(string $name, $value, ParamsValidator $validator) : ValidationResult
    {
        if ($value === null) {
            return ValidationResult::errorResult("null is not allowed for '$name'.");
        }
        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
    }
}
