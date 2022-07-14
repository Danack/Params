<?php

declare(strict_types = 1);

namespace TypeSpec\ProcessRule;

use TypeSpec\DataStorage\DataStorage;
use TypeSpec\OpenApi\ParamDescription;
use TypeSpec\ProcessedValues;
use TypeSpec\ValidationResult;

/**
 * Rule that ends processing with a set value.
 *
 * Used for testing.
 */
class AlwaysEndsRule implements ProcessPropertyRule
{
    /** @var mixed */
    private $finalValue;

    /**
     * @param mixed $finalResult
     */
    public function __construct($finalResult)
    {
        $this->finalValue = $finalResult;
    }

    /**
     * @param mixed $value
     * @param ProcessedValues $processedValues
     * @param DataStorage $inputStorage
     * @return ValidationResult
     */
    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {
        return ValidationResult::finalValueResult($this->finalValue);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        // Does nothing.
    }
}
