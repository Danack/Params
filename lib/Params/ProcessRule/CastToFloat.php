<?php

declare(strict_types = 1);

namespace Params\ProcessRule;

use Params\DataStorage\DataStorage;
use Params\Messages;
use Params\OpenApi\ParamDescription;
use Params\ProcessedValues;
use Params\ValidationResult;

/**
 * Takes user input and converts it to an float value, or
 * generates appropriate validationProblems.
 */
class CastToFloat implements ProcessRule
{
    /**
     * Convert a generic input value to an integer
     *
     * @param mixed $value
     * @param ProcessedValues $processedValues
     * @param DataStorage $inputStorage
     * @return ValidationResult
     */
    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {

        if (is_scalar($value) !== true) {
            $message = sprintf(
                Messages::FLOAT_REQUIRED_WRONG_TYPE,
                gettype($value)
            );

            return ValidationResult::errorResult(
                $inputStorage,
                $message
            );
        }

        if (is_int($value) !== true) {
            $value = (string)$value;
            if (strlen($value) === 0) {
                return ValidationResult::errorResult(
                    $inputStorage,
                    Messages::NEED_FLOAT_NOT_EMPTY_STRING
                );
            }

            if (strpos($value, ' ') !== false) {
                return ValidationResult::errorResult(
                    $inputStorage,
                    Messages::FLOAT_REQUIRED_FOUND_WHITESPACE
                );
            }

            $match = preg_match(
                "~        #delimiter
                    ^           # start of input
                    -?          # minus, optional
                    [0-9]+      # at least one digit
                    (           # begin group
                        \.      # a dot
                        [0-9]+  # at least one digit
                    )           # end of group
                    ?           # group is optional
                    $           # end of input
                ~xD",
                $value
            );

            if ($match !== 1) {
                // TODO - says what position bad character is at.
                return ValidationResult::errorResult(
                    $inputStorage,
                    Messages::FLOAT_REQUIRED
                );
            }
        }

        return ValidationResult::valueResult(floatval($value));
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::FORMAT_FLOAT);
    }
}
