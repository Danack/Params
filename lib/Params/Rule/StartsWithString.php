<?php

declare(strict_types=1);

namespace Params\Rule;

use Params\Rule;
use Params\ValidationResult;
use Params\OpenApi\ParamDescription;

/**
 * Convert the value to null if the string is empty, and provides
 * a final result
 */
class StartsWithString implements Rule
{
    /** @var string  */
    private $prefix;

    public function __construct(string $prefix)
    {
        $this->prefix = $prefix;
    }

    public function __invoke(string $name, $value): ValidationResult
    {
        if (strpos((string)$value, $this->prefix) !== 0) {
            $message = sprintf(
                "The string for [%s] must start with [%s].",
                $name,
                $this->prefix
            );

            return ValidationResult::errorResult($message);
        }

        // This rule does not modify the value
        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        // If we are allowing null, then parameter must be nullable
        // right?
        $paramDescription->setNullAllowed();
    }
}
