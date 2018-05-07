<?php

declare(strict_types=1);

namespace Params\Rule;

use Params\Rule;
use Params\ValidationResult;

class Trim implements Rule
{
    public function __invoke(string $name, $value): ValidationResult
    {
        return ValidationResult::valueResult(trim($value));
    }
}
