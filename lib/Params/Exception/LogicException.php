<?php

declare(strict_types=1);

namespace Params\Exception;

/**
 * Class LogicException
 * You have called something that has no meaning.
 */
class LogicException extends ParamsException
{
    public const ONLY_KEYS = "Processed values must have string keys";

    public const ONLY_INT_KEYS = "Key for array must be integer";

    public const MISSING_VALUE = "Trying to access [%s] which isn't present in ParamValuesImpl.";

    public const NOT_VALIDATION_PROBLEM = "Array must contain only 'ValidationProblem's instead got [%s]";

    public static function keysMustBeStrings(): self
    {
        return new self(self::ONLY_KEYS);
    }

    /**
     * @param mixed $wrongType
     * @return self
     */
    public static function onlyInputParameters($wrongType): self
    {
        return new self(sprintf(self::NOT_VALIDATION_PROBLEM, gettype($wrongType)));
    }

    public static function keysMustBeIntegers(): self
    {
        return new self(self::ONLY_INT_KEYS);
    }

    public static function missingValue(string $name): self
    {
        return new self(sprintf(self::MISSING_VALUE, $name));
    }
}
