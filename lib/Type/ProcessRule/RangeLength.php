<?php

declare(strict_types = 1);

namespace Type\ProcessRule;

use Type\DataStorage\DataStorage;
use Type\Messages;
use Type\OpenApi\ParamDescription;
use Type\ProcessedValues;
use Type\ValidationResult;

class RangeLength implements ProcessPropertyRule
{
    use CheckString;

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
        DataStorage $inputStorage
    ): ValidationResult {

        $value = $this->checkString($value);


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
