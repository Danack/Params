<?php

declare(strict_types=1);

namespace Params\ProcessRule;

use Params\DataLocator\DataLocator;
use Params\Messages;
use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ParamsValuesImpl;
use Params\ParamValues;
use Params\Path;

class ValidDatetime implements ProcessRule
{

    public function process(Path $path, $value, ParamValues $validator, DataLocator $dataLocator) : ValidationResult
    {
        $value = (string)$value;

        $dateTime = \DateTime::createFromFormat(\DateTime::RFC3339, $value);
        if ($dateTime instanceof \DateTime) {
            return ValidationResult::valueResult($dateTime);
        }

//        $dateTime = \DateTime::createFromFormat(\DateTime::RFC3339_EXTENDED, $value);
//        if ($dateTime instanceof \DateTime) {
//            return ValidationResult::valueResult($dateTime);
//        }

// todo - is there any value in returning these errors?
//            if (count($lastErrors['warnings']) !== 0 || count($lastErrors['errors']) !== 0) {
//                //$errorsArray = array_merge($lastErrors['warnings'], $lastErrors['errors']);
//                return ValidationResult::errorResult(
//                    self::ERROR_INVALID_DATETIME // . implode(". ", $errorsArray)
//                );
//            }

        return ValidationResult::errorResult($dataLocator, Messages::ERROR_INVALID_DATETIME);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_STRING);
        $paramDescription->setFormat(ParamDescription::FORMAT_DATETIME);
    }
}
