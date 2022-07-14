<?php

declare(strict_types = 1);

namespace TypeSpec\ProcessRule;

use TypeSpec\DataStorage\DataStorage;
use TypeSpec\Messages;
use TypeSpec\OpenApi\ParamDescription;
use TypeSpec\ProcessedValues;
use TypeSpec\ValidationResult;

/**
 * Convert the value to null if the string is empty, and provides
 * a final result
 */
class StartsWithString implements ProcessPropertyRule
{
    use CheckString;

    private string $prefix;

    public function __construct(string $prefix)
    {
        $this->prefix = $prefix;
    }

    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {

        $value = $this->checkString($value);

        if (strpos($value, $this->prefix) !== 0) {
            $message = sprintf(
                Messages::STRING_REQUIRES_PREFIX,
                $this->prefix
            );

            return ValidationResult::errorResult($inputStorage, $message);
        }

        // This rule does not modify the value
        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        // TODO implement
    }
}
