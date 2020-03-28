<?php

declare(strict_types = 1);

namespace ParamsTest\Integration;

use Params\OpenApi\ParamDescription;
use Params\ParamValues;
use Params\ProcessRule\ProcessRule;
use Params\ValidationProblem;
use Params\ValidationResult;
use Params\Path;

/**
 * @coversNothing
 * Example of processing an array, without processing each item individually
 * as a separate type.
 */
class ArrayAllMultiplesOf implements ProcessRule
{
    /** @var int */
    private $multiplicand;

    /**
     *
     * @param int $multiplicand
     */
    public function __construct(int $multiplicand)
    {
        $this->multiplicand = $multiplicand;
    }

    /**
     * @param Path $path
     * @param mixed $value
     * @param ParamValues $validator
     * @return ValidationResult
     */
    public function process(Path $path, $value, ParamValues $validator): ValidationResult
    {
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

                $errors[] = new ValidationProblem($path, $message);
            }
            $index += 1;
        }

        if (count($errors) !== 0) {
            return ValidationResult::thisIsMultipleErrorResult($errors);
        }

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        throw new \Exception("updateParamDescription not implemented yet.");
    }
}
