<?php

declare(strict_types=1);

namespace Params\ProcessRule;

use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ParamsValuesImpl;
use Params\ParamValues;
use Params\Path;

/**
 * Takes user input and converts it to an int value, or
 * generates appropriate validationProblems.
 */
class IntegerInput implements ProcessRule
{
    const MAX_SANE_VALUE = 999_999_999_999_999;

    /**
     * Convert a generic input value to an integer
     *
     * @param Path $path
     * @param mixed $value
     * @param ParamValues $validator
     * @return ValidationResult
     */
    public function process(Path $path, $value, ParamValues $validator) : ValidationResult
    {
        // TODO - check is null
        if (is_int($value) !== true) {
            $value = (string)$value;
            // check string length is not zero length.
            if (strlen($value) === 0) {
                return ValidationResult::errorResult(
                    $path,
                    "Value is an empty string - must be an integer."
                );
            }

            //Check only digits.
            $match = preg_match(
                "~        #delimiter
                    ^           # start of input
                    -?          # minus, optional
                    [0-9]+      # at least one digit
                    $           # end of input
                ~xD",
                $value
            );

            if ($match !== 1) {
                return ValidationResult::errorResult($path, "Value must contain only digits.");
            }
        }

        $maxSaneLength = strlen((string)(self::MAX_SANE_VALUE));

        if (strlen((string)$value) > $maxSaneLength) {
            $message = sprintf(
                "Value for %s too long, max %s digits",
                $path->toString(),
                $maxSaneLength
            );

            return ValidationResult::errorResult($path, $message);
        }

        return ValidationResult::valueResult(intval($value));
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        // todo - this seems like a not needed rule.
    }
}
