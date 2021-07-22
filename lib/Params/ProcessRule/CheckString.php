<?php

declare(strict_types = 1);

namespace Params\ProcessRule;

use Params\Exception\InvalidRulesException;

trait CheckString
{
    public function checkString(mixed $value): void
    {
        if (is_string($value) !== true) {
            throw InvalidRulesException::expectsStringForProcessing(get_called_class());
        }
    }
}
