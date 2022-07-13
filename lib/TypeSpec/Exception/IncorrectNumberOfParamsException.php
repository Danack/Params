<?php

declare(strict_types=1);


namespace TypeSpec\Exception;

use TypeSpec\Messages;

class IncorrectNumberOfParamsException extends ParamsException
{
    public static function wrongNumber(string $classname, int $expected, int $available): self
    {
        $message = sprintf(
            Messages::INCORRECT_NUMBER_OF_PARAMS,
            $classname,
            $expected,
            $available
        );

        return new self($message);
    }
}
