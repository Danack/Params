<?php

declare(strict_types = 1);

namespace Type\ProcessRule;

use Type\DataStorage\DataStorage;
use Type\Messages;
use Type\OpenApi\ParamDescription;
use Type\ProcessedValues;
use Type\ValidationResult;

/**
 * Checks that the value is one of a known set of values
 */
class Enum implements ProcessPropertyRule
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
        if (in_array($value, $this->allowedValues, true) !== true) {
            $message = sprintf(
                Messages::ENUM_MAP_UNRECOGNISED_VALUE_SINGLE,
                var_export($value, true), // This is sub-optimal
                implode(', ', $this->allowedValues)
            );

            return ValidationResult::errorResult(
                $inputStorage,
                $message
            );
        }

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setEnum($this->allowedValues);
    }
}
