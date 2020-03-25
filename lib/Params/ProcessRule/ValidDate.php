<?php

declare(strict_types=1);

namespace Params\ProcessRule;

use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ParamsValuesImpl;
use Params\ParamValues;
use Params\Path;

class ValidDate implements ProcessRule
{
    const ERROR_INVALID_DATETIME = 'Value was not a valid RFC3339 date apparently';

    public function process(Path $path, $value, ParamValues $validator) : ValidationResult
    {
        $dateTime = \DateTimeImmutable::createFromFormat('Y-m-d', $value);
        if ($dateTime instanceof \DateTimeImmutable) {
            $dateTime = $dateTime->setTime(0, 0, 0, 0);
            return ValidationResult::valueResult($dateTime);
        }

        return ValidationResult::errorResult($path->toString(), self::ERROR_INVALID_DATETIME);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_STRING);
        $paramDescription->setFormat(ParamDescription::FORMAT_DATE);
    }
}
