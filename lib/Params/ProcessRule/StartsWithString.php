<?php

declare(strict_types=1);

namespace Params\ProcessRule;

use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ParamsValuesImpl;
use Params\ParamValues;
use Params\Path;

/**
 * Convert the value to null if the string is empty, and provides
 * a final result
 */
class StartsWithString implements ProcessRule
{
    private string $prefix;

    public function __construct(string $prefix)
    {
        $this->prefix = $prefix;
    }

    public function process(Path $path, $value, ParamValues $validator) : ValidationResult
    {
        if (strpos((string)$value, $this->prefix) !== 0) {
            $message = sprintf(
                "The string for [%s] must start with [%s].",
                $path,
                $this->prefix
            );

            return ValidationResult::errorResult($path->toString(), $message);
        }

        // This rule does not modify the value
        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        // If we are allowing null, then parameter must be nullable
        // right?
        $paramDescription->setNullAllowed();
    }
}
