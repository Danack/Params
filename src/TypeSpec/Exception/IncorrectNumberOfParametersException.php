<?php

declare(strict_types=1);


namespace TypeSpec\Exception;

use TypeSpec\Messages;

class IncorrectNumberOfParametersException extends TypeSpecException
{
    public static function wrongNumber(string $classname, int $expected, int $available): self
    {
        $message = sprintf(
            Messages::INCORRECT_NUMBER_OF_PARAMETERS,
            $classname,
            $expected,
            $available
        );

        return new self($message);
    }
}
