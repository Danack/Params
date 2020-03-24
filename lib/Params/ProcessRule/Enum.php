<?php

declare(strict_types=1);

namespace Params\ProcessRule;

use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ParamsValuesImpl;
use Params\ParamValues;

/**
 * Checks that the value is one of a known set of values
 */
class Enum implements ProcessRule
{
    /**
     * @var array<mixed>
     */
    private array $allowedValues;

    /**
     *
     * @param array<mixed> $allowedValues
     */
    public function __construct(array $allowedValues)
    {
        $this->allowedValues = $allowedValues;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @param ParamValues $validator
     * @return ValidationResult
     */
    public function process(string $name, $value, ParamValues $validator): ValidationResult
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
