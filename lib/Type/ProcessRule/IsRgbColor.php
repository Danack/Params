<?php

declare(strict_types=1);


namespace Type\ProcessRule;

use Type\DataStorage\DataStorage;
use Type\Messages;
use Type\OpenApi\ParamDescription;
use Type\ProcessedValues;
use Type\ValidationResult;

/**
 * Class RgbColorRule
 * Validates an RGB or RGBA color string
 */
class IsRgbColor implements ProcessPropertyRule
{
    use CheckString;

    public const BAD_COLOR_STRING = "Input [%s] does not look like a valid color string.";

    // TODO - support these https://developer.mozilla.org/en-US/docs/Web/CSS/color_value
    // properly
    //
    // Hexadecimal notation: #RRGGBB[AA]
    //R (red), G (green), B (blue), and A (alpha) are hexadecimal characters (0–9, A–F). A is optional. For example, #ff0000 is equivalent to #ff0000ff.
    //Hexadecimal notation: #RGB[A]
    //R (red), G (green), B (blue), and A (alpha) are hexadecimal characters (0–9, A–F). A is optional. The three-digit notation (#RGB) is a shorter version of the six-digit form (#RRGGBB). For example, #f09 is the same color as #ff0099. Likewise, the four-digit RGB notation (#RGBA) is a shorter version of the eight-digit form (#RRGGBBAA). For example, #0f38 is the same color as #00ff3388.
    //Functional notation: rgb[a](R, G, B[, A])
    //R (red), G (green), and B (blue) can be either <number>s or <percentage>s, where the number 255 corresponds to 100%. A (alpha) can be a <number> between 0 and 1, or a <percentage>, where the number 1 corresponds to 100% (full opacity).


    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {

        $value = $this->checkString($value);

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
