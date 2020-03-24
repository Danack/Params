<?php

declare(strict_types=1);

namespace Params\ProcessRule;

use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ParamsValuesImpl;
use Params\ParamValues;

/**
 * @TODO - is there any point to this rule?
 */
class NotNull implements ProcessRule
{
    public function process(string $name, $value, ParamValues $validator) : ValidationResult
    {
        if ($value === null) {
            return ValidationResult::errorResult($name, "null is not allowed for '$name'.");
        }
        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
    }
}
