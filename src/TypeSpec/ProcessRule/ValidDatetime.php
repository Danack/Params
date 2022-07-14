<?php

declare(strict_types = 1);

namespace TypeSpec\ProcessRule;

use TypeSpec\DataStorage\DataStorage;
use TypeSpec\Messages;
use TypeSpec\OpenApi\ParamDescription;
use TypeSpec\ProcessedValues;
use TypeSpec\ValidationResult;

class ValidDatetime implements ProcessPropertyRule
{
    use CheckString;

    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {

        $value  = $this->checkString($value);

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

        return ValidationResult::errorResult($inputStorage, Messages::ERROR_INVALID_DATETIME);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_STRING);
        $paramDescription->setFormat(ParamDescription::FORMAT_DATETIME);
    }
}
