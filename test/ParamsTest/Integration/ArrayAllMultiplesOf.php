<?php

declare(strict_types = 1);

namespace ParamsTest\Integration;

use Params\OpenApi\ParamDescription;
use Params\ParamValues;
use Params\SubsequentRule\SubsequentRule;
use Params\ValidationResult;

class ArrayAllMultiplesOf implements SubsequentRule
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


    public function process(string $name, $value, ParamValues $validator): ValidationResult
    {
        $errors = [];

        $index = 0;
        foreach ($value as $item) {
            if (($item % $this->multiplicand) !== 0) {
                $errors[] = sprintf(
                    "Item at position %s in not a multiple of %s but has value [%s]",
                    $index,
                    $this->multiplicand,
                    $item
                );
            }
            $index += 1;
        }

        if (count($errors) !== 0) {
            return ValidationResult::errorsResult($errors);
        }

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        throw new \Exception("updateParamDescription not implemented yet.");
    }
}
