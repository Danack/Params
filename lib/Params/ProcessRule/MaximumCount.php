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

class MaximumCount implements ProcessRule
{
    private int $maximumCount;

    /**
     *
     * @param int $maximumCount The maximum number (inclusive) of elements
     */
    public function __construct(int $maximumCount)
    {
        if ($maximumCount < 0) {
            throw new LogicException(Messages::ERROR_MAXIMUM_COUNT_MINIMUM);
        }

        $this->maximumCount = $maximumCount;
    }

    public function process(Path $path, $value, ParamValues $validator) : ValidationResult
    {
        if (is_array($value) !== true) {
            $message = sprintf(
                Messages::ERROR_WRONG_TYPE_VARIANT_1,
                gettype($value)
            );

            throw new LogicException($message);
        }

        $actualCount = count($value);

        if ($actualCount > $this->maximumCount) {
            $message = sprintf(
                Messages::ERROR_TOO_MANY_ELEMENTS,
                $path->toString(),
                $this->maximumCount,
                $actualCount
            );

            return ValidationResult::errorResult($path, $message);
        }

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setMinItems($this->maximumCount);
        $paramDescription->setExclusiveMinimum(false);
    }
}
