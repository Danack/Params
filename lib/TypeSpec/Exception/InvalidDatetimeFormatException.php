<?php

declare(strict_types = 1);

namespace TypeSpec\Exception;

use TypeSpec\Messages;

class InvalidDatetimeFormatException extends \TypeSpec\Exception\TypeSpecException
{
    /**
     * Only strings are allowed datetime format.
     * @param int $index
     * @param mixed $nonStringVariable
     * @return InvalidDatetimeFormatException
     */
    public static function stringRequired(int $index, $nonStringVariable): self
    {
        $message = sprintf(
            Messages::ERROR_DATE_FORMAT_MUST_BE_STRING,
            gettype($nonStringVariable),
            $index
        );

        return new self($message);
    }
}
