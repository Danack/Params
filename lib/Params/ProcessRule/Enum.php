<?php

declare(strict_types = 1);

namespace Params\ProcessRule;

use Params\InputStorage\InputStorage;
use Params\Messages;
use Params\OpenApi\ParamDescription;
use Params\ProcessedValues;
use Params\ValidationResult;

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
     * @param mixed $value
     * @param ProcessedValues $processedValues
     * @param InputStorage $inputStorage
     * @return ValidationResult
     */
    public function process(
        $value,
        ProcessedValues $processedValues,
        InputStorage $inputStorage
    ): ValidationResult {
        if (in_array($value, $this->allowedValues, true) !== true) {
            $message = sprintf(
                Messages::ENUM_MAP_UNRECOGNISED_VALUE_SINGLE,
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
