<?php

declare(strict_types = 1);

namespace Params\ProcessRule;

use Params\InputStorage\InputStorage;
use Params\Messages;
use Params\OpenApi\ParamDescription;
use Params\ProcessedValues;
use Params\ValidationResult;

class MatrixIsOddSized implements ProcessRule
{
    public function process(
        $value,
        ProcessedValues $processedValues,
        InputStorage $inputStorage
    ): ValidationResult {

        /** @var array<array<float>> $value   */
        $outerDimension = count($value);

        if (($outerDimension % 2) === 0) {
            return ValidationResult::errorResult(
                $inputStorage,
                Messages::MATRIX_MUST_BE_ODD_SIZED_ROWS_ARE_EVEN
            );
        }

        $innerDimension = count($value[0]);

        if (($innerDimension % 2) === 0) {
            return ValidationResult::errorResult(
                $inputStorage,
                Messages::MATRIX_MUST_BE_ODD_SIZED_COLUMNS_ARE_EVEN
            );
        }

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
//        $paramDescription->setMaxLength($this->maxLength);
    }
}
