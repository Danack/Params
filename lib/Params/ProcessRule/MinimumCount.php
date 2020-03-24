<?php

declare(strict_types = 1);

namespace Params\ProcessRule;

use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\Exception\LogicException;
use Params\ParamsValuesImpl;
use Params\ParamValues;

class MinimumCount implements ProcessRule
{
    private int $minimumCount;

    public const ERROR_TOO_FEW_ELEMENTS = "Number of elements in %s is too small. Min allowed is %d but only got %d.";

    public const ERROR_MINIMUM_COUNT_MINIMUM = "Minimum count must be zero or above.";

    public const ERROR_WRONG_TYPE = "Minimum count can only be applied to an array but tried to operate on %s.";

    /**
     * @param int $minimumCount the minimum number (inclusive) of elements.
     */
    public function __construct(int $minimumCount)
    {
        if ($minimumCount < 0) {
            throw new LogicException(self::ERROR_MINIMUM_COUNT_MINIMUM);
        }

        $this->minimumCount = $minimumCount;
    }

    public function process(string $name, $value, ParamValues $validator) : ValidationResult
    {
        if (is_array($value) !== true) {
            $message = sprintf(
                self::ERROR_WRONG_TYPE,
                gettype($value)
            );

            throw new LogicException($message);
        }

        $actualCount = count($value);

        if ($actualCount < $this->minimumCount) {
            $message = sprintf(
                self::ERROR_TOO_FEW_ELEMENTS,
                $name,
                $this->minimumCount,
                $actualCount
            );

            return ValidationResult::errorResult($name, $message);
        }

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setMinItems($this->minimumCount);
        $paramDescription->setExclusiveMinimum(false);
    }
}
