<?php

declare(strict_types = 1);

namespace Params\SubsequentRule;

use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\Exception\LogicException;
use Params\ParamsValidator;

class MinimumCount implements SubsequentRule
{
    /** @var int  */
    private $minimumCount;

    public const ERROR_TOO_FEW_ELEMENTS = "Number of elements in %s is too small. Min allowed is %d but only got %d.";

    /**
     * @param int $minimumCount the minimum number (inclusive) of elements.
     */
    public function __construct(int $minimumCount)
    {
        $this->minimumCount = $minimumCount;
    }

    public function process(string $name, $value, ParamsValidator $validator) : ValidationResult
    {
        if (is_array($value) !== true) {
            sprintf(
                "Minimum count can only be applied to an array but tried to operate on %s ",
                gettype($value)
            );

            throw new LogicException();
        }

        $actualCount = count($value);

        if ($actualCount < $this->minimumCount) {
            $message = sprintf(
                self::ERROR_TOO_FEW_ELEMENTS,
                $name,
                $this->minimumCount,
                $actualCount
            );

            return ValidationResult::errorResult($message);
        }

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        $paramDescription->setMinItems($this->minimumCount);
        $paramDescription->setExclusiveMinimum(false);
    }
}
