<?php

declare(strict_types = 1);

namespace Params\ProcessRule;

use Params\InputStorage\InputStorage;
use Params\Messages;
use Params\OpenApi\ParamDescription;
use Params\ProcessedValues;
use Params\ValidationResult;

class RangeLength implements ProcessRule
{
    private int $minLength;

    private int $maxLength;

    /**
     * MaxLengthValidator constructor.
     * @param int $maxLength
     */
    public function __construct(
        int $minLength,
        int $maxLength
    ) {
        $this->minLength = $minLength;
        $this->maxLength = $maxLength;
    }

    public function process(
        $value,
        ProcessedValues $processedValues,
        InputStorage $inputStorage
    ): ValidationResult {

        // TODO - handle to string conversion better.
        $value = (string)$value;

        // Check min length
        if (mb_strlen($value) < $this->minLength) {
            $message = sprintf(
                Messages::STRING_TOO_SHORT,
                $this->minLength
            );

            return ValidationResult::errorResult(
                $inputStorage,
                $message
            );
        }

        // Check max length
        if (mb_strlen($value) > $this->maxLength) {
            $message = sprintf(
                Messages::STRING_TOO_LONG,
                $this->maxLength
            );
            return ValidationResult::errorResult($inputStorage, $message);
        }

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setMinLength($this->minLength);
        $paramDescription->setMaxLength($this->maxLength);
    }
}
