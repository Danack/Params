<?php

declare(strict_types = 1);

namespace Params\ProcessRule;

use Params\DataLocator\InputStorageAye;
use Params\Messages;
use Params\OpenApi\ParamDescription;
use Params\ProcessedValues;
use Params\ValidationResult;

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

    public function process(
        $value,
        ProcessedValues $processedValues,
        InputStorageAye $dataLocator
    ): ValidationResult {
        if (strpos((string)$value, $this->prefix) !== 0) {
            $message = sprintf(
                Messages::STRING_REQUIRES_PREFIX,
                $this->prefix
            );

            return ValidationResult::errorResult($dataLocator, $message);
        }

        // This rule does not modify the value
        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        // TODO implement
    }
}
