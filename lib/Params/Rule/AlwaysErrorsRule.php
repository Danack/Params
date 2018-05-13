<?php

declare(strict_types=1);

namespace Params\Rule;

use Params\Rule;
use Params\ValidationResult;

class AlwaysErrorsRule implements Rule
{
    /** @var string */
    private $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function __invoke(string $name, $value): ValidationResult
    {
        return ValidationResult::errorResult($this->message);
    }
}
