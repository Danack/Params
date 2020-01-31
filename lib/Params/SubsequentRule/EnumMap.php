<?php

declare(strict_types=1);

namespace Params\SubsequentRule;

use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ParamsValidator;
use Params\ParamValues;

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
class EnumMap implements SubsequentRule
{
    /** @var array */
    private $allowedValues;

    public function __construct(array $allowedValues)
    {
        $this->allowedValues = $allowedValues;
    }

    public function process(string $name, $value, ParamValues $validator) : ValidationResult
    {


        if (array_key_exists($value, $this->allowedValues) !== true) {
            return ValidationResult::errorResult(
                $name,
                "Value is not known. Please use one of " . implode(', ', $this->allowedValues)
            );
        }

        return ValidationResult::valueResult($this->allowedValues[$value]);
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
//        $paramDescription->setEnum($this->allowedValues);
    }
}
