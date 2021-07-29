<?php

declare(strict_types = 1);

namespace Params\ProcessRule;

use Params\DataStorage\DataStorage;
use Params\Messages;
use Params\OpenApi\ParamDescription;
use Params\ProcessedValues;
use Params\ValidationResult;

class MatrixIsSize implements ProcessRule
{
    public function __construct(
        private ?int $expected_row_count,
        private ?int $expected_column_count
    ) {
    }

    /**
     * @param array<array<float>> $value
     * @return string|null
     */
    private function checkRowAndColumnSize($value): ?string
    {
        if ($this->expected_row_count === null ||
            $this->expected_column_count === null) {
            return null;
        }

        $outerDimension = count($value);
        $innerDimension = count($value[0]);
        if ($outerDimension !== $this->expected_row_count) {
            $message = sprintf(
                Messages::MATRIX_MUST_BE_OF_SIZE,
                $this->expected_row_count,
                $this->expected_column_count,
                $outerDimension,
                $innerDimension
            );

            return $message;
        }

        if ($innerDimension !== $this->expected_column_count) {
            $message = sprintf(
                Messages::MATRIX_MUST_BE_OF_SIZE,
                $this->expected_row_count,
                $this->expected_column_count,
                $outerDimension,
                $innerDimension
            );

            return $message;
        }


        return null;
    }

    /**
     * @param array<array<float>> $value
     * @return string|null
     */
    private function checkRowSize($value): ?string
    {
        if ($this->expected_row_count === null) {
            return null;
        }

        $outerDimension = count($value);
        if ($outerDimension !== $this->expected_row_count) {
            return sprintf(
                Messages::MATRIX_MUST_BE_OF_ROW_SIZE,
                $this->expected_row_count,
                $outerDimension
            );
        }
        return null;
    }

    /**
     * @param array<array<float>> $value
     * @return string|null
     */
    private function checkColumnSize($value): ?string
    {
        if ($this->expected_column_count === null) {
            return null;
        }

        $innerDimension = count($value[0]);
        if ($innerDimension !== $this->expected_column_count) {
            return sprintf(
                Messages::MATRIX_MUST_BE_OF_COLUMN_SIZE,
                $this->expected_column_count,
                $innerDimension
            );
        }
        return null;
    }



    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {

        /** @var array<array<float>> $value   */
        $message = $this->checkRowAndColumnSize($value);
        if ($message !== null) {
            return ValidationResult::errorResult($inputStorage, $message);
        }
        $message = $this->checkRowSize($value);
        if ($message !== null) {
            return ValidationResult::errorResult($inputStorage, $message);
        }
        $message = $this->checkColumnSize($value);
        if ($message !== null) {
            return ValidationResult::errorResult($inputStorage, $message);
        }
        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
//        $paramDescription->setMaxLength($this->maxLength);
    }
}
