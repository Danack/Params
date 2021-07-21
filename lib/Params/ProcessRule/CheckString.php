<?php

declare(strict_types = 1);

namespace Params\ProcessRule;

use Params\Exception\InvalidRulesException;

trait CheckString
{
    public function checkString(mixed $value)
    {
        if (is_string($value) !== true) {
            throw  \Params\Exception\InvalidRulesException::expectsStringForProcessing
        }
    }
}
