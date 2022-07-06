<?php

declare(strict_types = 1);

namespace Type\ProcessRule;

use Type\DataStorage\DataStorage;
use Type\Exception\LogicException;
use Type\Messages;
use Type\OpenApi\ParamDescription;
use Type\ProcessedValues;
use Type\ValidationResult;

class MinimumCount implements ProcessPropertyRule
{
    private int $minimumCount;

    /**
     * @param int $minimumCount the minimum number (inclusive) of elements.
     */
    public function __construct(int $minimumCount)
    {
        if ($minimumCount < 0) {
            throw new LogicException(Messages::ERROR_MINIMUM_COUNT_MINIMUM);
        }

        $this->minimumCount = $minimumCount;
    }

    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {
        if (is_array($value) !== true) {
            $message = sprintf(
                Messages::ERROR_WRONG_TYPE,
                gettype($value)
            );

            throw new LogicException($message);
        }

        $actualCount = count($value);

        if ($actualCount < $this->minimumCount) {
            $message = sprintf(
                Messages::ERROR_TOO_FEW_ELEMENTS,
                $this->minimumCount,
                $actualCount
            );

            return ValidationResult::errorResult($inputStorage, $message);
        }

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setMinItems($this->minimumCount);
        $paramDescription->setExclusiveMinimum(false);
    }
}
