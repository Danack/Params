<?php

declare(strict_types=1);

namespace Params\ProcessRule;

use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ParamsValuesImpl;
use Params\ParamValues;
use Params\Path;

/**
 * @TODO - is there any point to this rule?
 */
class NotNull implements ProcessRule
{
    public function process(Path $path, $value, ParamValues $validator) : ValidationResult
    {
        if ($value === null) {
            return ValidationResult::errorResult(
                $path,
                "null is not allowed for '" . $path->toString() . "'."
            );
        }
        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
    }
}
