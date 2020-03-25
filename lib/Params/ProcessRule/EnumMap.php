<?php

declare(strict_types=1);

namespace Params\ProcessRule;

use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ParamValues;
use Params\Exception\InvalidRulesException;
use Params\Path;

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
    /** @var array<mixed>  */
    private array $allowedValues;

    /**
     * @param array<mixed> $allowedValues
     */
    public function __construct(array $allowedValues)
    {
        $this->allowedValues = $allowedValues;
    }

    public function process(Path $path, $value, ParamValues $validator) : ValidationResult
    {
        if (is_int($value) === false && is_string($value) === false) {
            throw InvalidRulesException::badTypeForArrayAccess($value);
        }

        if (array_key_exists($value, $this->allowedValues) !== true) {
            $allowedInputValues = implode(', ', array_keys($this->allowedValues));

            return ValidationResult::errorResult(
                $path,
                "Value is not known. Please use one of " . $allowedInputValues
            );
        }

        return ValidationResult::valueResult($this->allowedValues[$value]);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
//        $paramDescription->setEnum($this->allowedValues);
    }
}
