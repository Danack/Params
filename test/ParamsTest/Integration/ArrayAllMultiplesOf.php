<?php

declare(strict_types = 1);

namespace ParamsTest\Integration;

use Params\DataLocator\InputStorageAye;
use Params\OpenApi\ParamDescription;
use Params\ProcessedValues;
use Params\ProcessRule\ProcessRule;
use Params\ValidationProblem;
use Params\ValidationResult;

/**
 * Example of processing an array, without processing each item individually
 * as a separate type.
 *
 * @coversNothing
 */
class ArrayAllMultiplesOf implements ProcessRule
{
    private int $multiplicand;

    /**
     *
     * @param int $multiplicand
     */
    public function __construct(int $multiplicand)
    {
        $this->multiplicand = $multiplicand;
    }

    /**
     * @param mixed $value
     * @param ProcessedValues $processedValues
     * @param InputStorageAye $dataLocator
     * @return ValidationResult
     */
    public function process(
        $value,
        ProcessedValues $processedValues,
        InputStorageAye $dataLocator
    ): ValidationResult {
        $errors = [];

        $index = 0;
        foreach ($value as $item) {
            if (($item % $this->multiplicand) !== 0) {
                // Because this is operating on an array of items, we need to put the complete name
                // not just the index
                $message = sprintf(
                    'Value at position [%d] is not a multiple of %s but has value [%s]',
                    $index,
                    $this->multiplicand,
                    $item
                );

                $errors[] = new ValidationProblem($dataLocator, $message);
            }
            $index += 1;
        }

        if (count($errors) !== 0) {
            return ValidationResult::fromValidationProblems($errors);
        }

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        throw new \Exception("updateParamDescription not implemented yet.");
    }
}
