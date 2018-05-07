<?php

declare(strict_types=1);

namespace Params\Rule;

use Params\Rule;
use Params\ValidationResult;

class ValidDatetime implements Rule
{
    const ERROR_INVALID_DATETIME = 'Value was not a valid date time.';

    public function __invoke(string $name, $value): ValidationResult
    {
        try {
            $dateTime = new \DateTime($value);
        }
        catch (\Exception $e) {
            return ValidationResult::errorResult(self::ERROR_INVALID_DATETIME);
        }
        // TODO - add sanity checks for being less than 10 years in the past or future.
        return ValidationResult::valueResult($dateTime);
    }
}
