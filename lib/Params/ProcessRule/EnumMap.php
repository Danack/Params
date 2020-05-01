<?php

declare(strict_types = 1);

namespace Params\ProcessRule;

use Params\DataLocator\InputStorageAye;
use Params\Exception\InvalidRulesException;
use Params\Messages;
use Params\OpenApi\ParamDescription;
use Params\ProcessedValues;
use Params\ValidationResult;

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
class EnumMap implements ProcessRule
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
        InputStorageAye $dataLocator
    ): ValidationResult {
        if (is_int($value) === false && is_string($value) === false) {
            throw InvalidRulesException::badTypeForArrayAccess($value);
        }

        if (array_key_exists($value, $this->allowedValues) !== true) {
            $allowedInputValues = implode(', ', array_keys($this->allowedValues));

            $message = sprintf(
                Messages::ENUM_MAP_UNRECOGNISED_VALUE_SINGLE,
                $allowedInputValues
            );

            return ValidationResult::errorResult(
                $dataLocator,
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
