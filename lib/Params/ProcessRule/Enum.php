<?php

declare(strict_types=1);

namespace Params\ProcessRule;

use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ParamsValidator;
use Params\ParamValues;

/**
 * Checks that the value is one of a known set of values
 */
class Enum implements ProcessRule
{
    private array $allowedValues;

    public function __construct(array $allowedValues)
    {
        $this->allowedValues = $allowedValues;
    }

    public function process(string $name, $value, ParamValues $validator) : ValidationResult
    {
        if (in_array($value, $this->allowedValues, true) !== true) {
            return ValidationResult::errorResult(
                $name,
                "Value is not known. Please use one of " . implode(', ', $this->allowedValues)
            );
        }

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setEnum($this->allowedValues);
    }
}
