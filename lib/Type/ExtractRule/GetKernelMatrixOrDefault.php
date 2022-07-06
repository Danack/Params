<?php

declare(strict_types=1);

namespace Type\ExtractRule;

use Type\DataStorage\DataStorage;
use Type\OpenApi\ParamDescription;
use Type\ProcessedValues;
use Type\ProcessRule\CastToFloat;
use Type\ValidationResult;
use Type\Exception\LogicException;
use Type\Messages;

class GetKernelMatrixOrDefault implements ExtractPropertyRule
{
    private ?array $default;

    /**
     * @param array $default
     */
    public function __construct(array $default)
    {
        foreach ($default as $row) {
            if (is_array($row) !== true) {
                throw new LogicException(Messages::MATRIX_INVALID_BAD_ROW);
            }

            foreach ($row as $value) {
                if (is_float($value) === false && is_int($value) === false) {
                    throw new LogicException(Messages::MATRIX_INVALID_BAD_CELL);
                }
            }
        }

        $this->default = $default;
    }

    public function process(
        ProcessedValues $processedValues,
        DataStorage $dataStorage
    ): ValidationResult {
        if ($dataStorage->isValueAvailable() !== true) {
            return ValidationResult::valueResult($this->default);
        }

        $currentValue = $dataStorage->getCurrentValue();

        if (is_string($currentValue) !== true) {
            // TODO - this represent a programmer error - should it
            // be an exception?

            throw new LogicException(Messages::BAD_TYPE_FOR_KERNEL_MATRIX_PROCESS_RULE);
        }

        // TODO - this needs to be replaced with something that gives the
        // precise location of the error....probably.
        $matrix_value = json_decode($currentValue, $associative = true, 4);
        $lastError = json_last_error();
        if ($lastError !== JSON_ERROR_NONE) {
            return ValidationResult::errorResult($dataStorage, "Error parsing matrix" . json_last_error_msg());
        }

        if (is_array($matrix_value) !== true) {
            $message = "2d array expected but value is " . var_export($matrix_value, true);
            return ValidationResult::errorResult($dataStorage, $message);
        }

        $validationProblems = [];
        $row_count = 0;
        $floatRule = new CastToFloat();

        foreach ($matrix_value as $row) {
            if (is_array($row) !== true) {
                return ValidationResult::errorResult($dataStorage, "Row $row_count - 2d array expected");
            }

            $column_count = 0;
            foreach ($row as $value) {
                if (is_float($value) === false && is_int($value) === false) {
                    $message = sprintf(
                        "Row %s column %s 2d array expected",
                        $row_count,
                        $column_count,
                    );

                    return ValidationResult::errorResult($dataStorage, $message);
                }

                $column_count += 1;
            }

            foreach ($row as $value) {
                $floatRuleResult = $floatRule->process(
                    $value,
                    $processedValues,
                    $dataStorage
                );

                if ($floatRuleResult->anyErrorsFound()) {
                    foreach ($floatRuleResult->getValidationProblems() as $validationProblem) {
                        $validationProblems[] = $validationProblem;
                    }
                }
            }

            $row_count += 1;
        }

        if (count($validationProblems) !== 0) {
            return ValidationResult::fromValidationProblems($validationProblems);
        }

        return ValidationResult::valueResult($matrix_value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_ARRAY);
        $paramDescription->setFormat('kernel_matrix');
//        $paramDescription->setDefault($this->default);
//        $paramDescription->setRequired(false);
    }
}
