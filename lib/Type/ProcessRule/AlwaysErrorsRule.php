<?php

declare(strict_types = 1);

namespace Type\ProcessRule;

use Type\DataStorage\DataStorage;
use Type\OpenApi\ParamDescription;
use Type\ProcessedValues;
use Type\ValidationResult;

/**
 * Rule that ends processing with an error ValidationResult with the
 * set error message.
 */
class AlwaysErrorsRule implements ProcessPropertyRule
{
    private string $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }


    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {
        return ValidationResult::errorResult($inputStorage, $this->message);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        // Does nothing.
    }
}
