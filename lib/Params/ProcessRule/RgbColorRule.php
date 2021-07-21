<?php

declare(strict_types=1);


namespace Params\ProcessRule;

use Params\InputStorage\InputStorage;
use Params\Messages;
use Params\OpenApi\ParamDescription;
use Params\ProcessedValues;
use Params\ValidationResult;
use

/**
 * Class RgbColorRule
 * Validates an RGB or RGBA color string
 */
class RgbColorRule implements ProcessRule
{
    use CheckString;

    public const BAD_COLOR_STRING = "Input [%s] does not look like a valid color string.";

    public function process(
        $value,
        ProcessedValues $processedValues,
        InputStorage $inputStorage
    ): ValidationResult {

        $this->checkString($value);

        // TODO - this is to imprecise. Need to match web css colors
        // more precisely
        // https://developer.mozilla.org/en-US/docs/Web/CSS/color_value
        // maybe https://www.regexpal.com/97509
        $allowed_patterns = [
            "rgb\(\d{1,3},\s*\d{1,3},\s*\d{1,3}\)",
            "rgba\(\d{1,3},\s*\d{1,3},\s*\d{1,3}, .*\)"
        ];

        foreach ($allowed_patterns as $allowed_pattern) {
            $pattern = "#" . $allowed_pattern . "#iu";
            $matched = preg_match($pattern, $value);
            if ($matched === 1) {
                return ValidationResult::finalValueResult($value);
            }
        }

        $string_start = substr(var_export($value, true), 0, 50);

        $message = sprintf(
            self::BAD_COLOR_STRING,
            $string_start
        );

        return ValidationResult::errorResult(
            $inputStorage,
            $message
        );
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setFormat('color');
    }
}
