<?php

declare(strict_types = 1);

namespace Params\ProcessRule;

use Params\InputStorage\InputStorage;
use Params\Messages;
use Params\OpenApi\ParamDescription;
use Params\ProcessedValues;
use Params\ValidationResult;

class ValidDate implements ProcessRule
{

    public function process(
        $value,
        ProcessedValues $processedValues,
        InputStorage $inputStorage
    ): ValidationResult {
        // TODO - should we handle this better?
        $value = (string)$value;
        $dateTime = \DateTimeImmutable::createFromFormat('Y-m-d', $value);
        if ($dateTime instanceof \DateTimeImmutable) {
            $dateTime = $dateTime->setTime(0, 0, 0, 0);
            return ValidationResult::valueResult($dateTime);
        }

        return ValidationResult::errorResult($inputStorage, Messages::ERROR_INVALID_DATETIME);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_STRING);
        $paramDescription->setFormat(ParamDescription::FORMAT_DATE);
    }
}
