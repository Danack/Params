<?php

declare(strict_types = 1);

namespace Params\ProcessRule;

use Params\DataStorage\DataStorage;
use Params\OpenApi\ParamDescription;
use Params\ProcessedValues;
use Params\ValidationResult;
use Params\Messages;

class MinLength implements ProcessRule
{
    use CheckString;

    private int $minLength;

    public function __construct(int $minLength)
    {
        $this->minLength = $minLength;
    }

    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {

        $value = $this->checkString($value);

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
        return ValidationResult::valueResult($value);
    }


    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setMinLength($this->minLength);
    }
}
