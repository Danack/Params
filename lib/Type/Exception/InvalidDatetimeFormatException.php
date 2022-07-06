<?php

declare(strict_types = 1);

namespace Type\Exception;

use Type\Messages;

class InvalidDatetimeFormatException extends \Type\Exception\ParamsException
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
