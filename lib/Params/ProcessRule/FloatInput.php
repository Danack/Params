<?php

declare(strict_types = 1);

namespace Params\ProcessRule;

use Params\DataLocator\InputStorageAye;
use Params\Messages;
use Params\OpenApi\ParamDescription;
use Params\ProcessedValues;
use Params\ValidationResult;

/**
 * Takes user input and converts it to an float value, or
 * generates appropriate validationProblems.
 */
class FloatInput implements ProcessRule
{
    /**
     * Convert a generic input value to an integer
     *
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

        if (is_scalar($value) !== true) {
            return ValidationResult::errorResult(
                $dataLocator,
                Messages::VALUE_MUST_BE_SCALAR,
            );
        }

        if (is_int($value) !== true) {
            $value = (string)$value;
            if (strlen($value) === 0) {
                return ValidationResult::errorResult(
                    $dataLocator,
                    Messages::NEED_FLOAT_NOT_EMPTY_STRING
                );
            }

            if (strpos($value, ' ') !== false) {
                return ValidationResult::errorResult(
                    $dataLocator,
                    Messages::NEED_FLOAT_WHITESPACE
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
                    $dataLocator,
                    Messages::NEED_FLOAT
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
