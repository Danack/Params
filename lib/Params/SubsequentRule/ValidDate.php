<?php

declare(strict_types=1);

namespace Params\SubsequentRule;

use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ParamsValidator;

class ValidDate implements SubsequentRule
{
    const ERROR_INVALID_DATETIME = 'Value was not a valid RFC3339 date apparently';

    public function process(string $name, $value, ParamsValidator $validator) : ValidationResult
    {
        $dateTime = \DateTimeImmutable::createFromFormat('Y-m-d', $value);
        if ($dateTime instanceof \DateTimeImmutable) {
            $dateTime = $dateTime->setTime(0, 0, 0, 0);
            return ValidationResult::valueResult($dateTime);
        }

        return ValidationResult::errorResult(self::ERROR_INVALID_DATETIME);
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        $paramDescription->setType(ParamDescription::TYPE_STRING);
        $paramDescription->setFormat(ParamDescription::FORMAT_DATE);
    }
}
