<?php

declare(strict_types = 1);

namespace Params\ProcessRule;

use Params\Messages;
use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\Exception\LogicException;
use Params\ParamsValuesImpl;
use Params\ParamValues;
use Params\Path;

class MinimumCount implements ProcessRule
{
    private int $minimumCount;

    /**
     * @param int $minimumCount the minimum number (inclusive) of elements.
     */
    public function __construct(int $minimumCount)
    {
        if ($minimumCount < 0) {
            throw new LogicException(Messages::ERROR_MINIMUM_COUNT_MINIMUM);
        }

        $this->minimumCount = $minimumCount;
    }

    public function process(Path $path, $value, ParamValues $validator) : ValidationResult
    {
        if (is_array($value) !== true) {
            $message = sprintf(
                Messages::ERROR_WRONG_TYPE,
                gettype($value)
            );

            throw new LogicException($message);
        }

        $actualCount = count($value);

        if ($actualCount < $this->minimumCount) {
            $message = sprintf(
                Messages::ERROR_TOO_FEW_ELEMENTS,
                $path->toString(),
                $this->minimumCount,
                $actualCount
            );

            return ValidationResult::errorResult($path, $message);
        }

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setMinItems($this->minimumCount);
        $paramDescription->setExclusiveMinimum(false);
    }
}
