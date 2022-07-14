<?php

declare(strict_types = 1);

namespace TypeSpec\ProcessRule;

use TypeSpec\Exception\InvalidRulesException;

trait CheckString
{
    public function checkString(mixed $value): string
    {
        if (is_string($value) === true) {
            return $value;
        }

        if (is_object($value) === true &&
            is_a($value, \Stringable::class) === true) {
            return (string)$value;
        }

        throw InvalidRulesException::expectsStringForProcessing(get_called_class());
    }
}
