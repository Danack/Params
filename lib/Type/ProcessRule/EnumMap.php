<?php

declare(strict_types = 1);

namespace Type\ProcessRule;

use Type\DataStorage\DataStorage;
use Type\Exception\InvalidRulesException;
use Type\Messages;
use Type\OpenApi\ParamDescription;
use Type\ProcessedValues;
use Type\ValidationResult;

/**
 * Checks that the value is one of a known set of input values and
 * then maps it to a different.
 *
 * e.g. for the enum map of:
 *
 * [
 *  'rgb' => Imagick::COLORSPACE_RGB,
 *  'hsl' => Imagick::COLORSPACE_HSL,
 * ]
 *
 * The user could pass in 'hsl' and the resulting value would be whatever the
 * Imagick::COLORSPACE_HSL constant is.
 *
 */
class EnumMap implements ProcessPropertyRule
{
    /** @var array<mixed> */
    private array $allowedValues;

    /**
     * @param array<mixed> $allowedValues
     */
    public function __construct(array $allowedValues)
    {
        $this->allowedValues = $allowedValues;
    }

    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {
        if (is_int($value) === false && is_string($value) === false) {
            throw InvalidRulesException::badTypeForArrayAccess($value);
        }

        if (array_key_exists($value, $this->allowedValues) !== true) {
            $allowedInputValues = implode(', ', array_keys($this->allowedValues));

            $message = sprintf(
                Messages::ENUM_MAP_UNRECOGNISED_VALUE_SINGLE,
                $value,
                $allowedInputValues
            );

            return ValidationResult::errorResult(
                $inputStorage,
                $message
            );
        }

        return ValidationResult::valueResult($this->allowedValues[$value]);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setEnum(array_keys($this->allowedValues));
    }
}
