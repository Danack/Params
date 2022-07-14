<?php

declare(strict_types = 1);

namespace TypeSpec\ProcessRule;

use TypeSpec\DataStorage\DataStorage;
use TypeSpec\Messages;
use TypeSpec\OpenApi\ParamDescription;
use TypeSpec\ProcessedValues;
use TypeSpec\ValidationResult;

class MatrixIsSquare implements ProcessPropertyRule
{


    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {

        /** @var array<array<float>> $value */

        $rowsDiminsion = count($value);
        $columnsDimension = count($value[0]);

        if ($rowsDiminsion !== $columnsDimension) {
            $message = sprintf(
                Messages::MATRIX_MUST_BE_SQUARE,
                $rowsDiminsion,
                $columnsDimension
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
//        $paramDescription->setMaxLength($this->maxLength);
    }
}
