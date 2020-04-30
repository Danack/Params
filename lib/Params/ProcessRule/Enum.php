<?php

declare(strict_types = 1);

namespace Params\ProcessRule;

use Params\DataLocator\InputStorageAye;
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
     * @param InputStorageAye $dataLocator
     * @return ValidationResult
     */
    public function process(
        $value,
        ProcessedValues $processedValues,
        InputStorageAye $dataLocator
    ): ValidationResult {
        if (in_array($value, $this->allowedValues, true) !== true) {
            $message = sprintf(
                Messages::ENUM_MAP_UKNOWN_VALUE,
                implode(', ', $this->allowedValues)
            );

            return ValidationResult::errorResult(
                $dataLocator,
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
