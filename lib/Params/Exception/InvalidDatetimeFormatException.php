<?php

declare(strict_types = 1);

namespace Params\Exception;

use Params\Messages;

class InvalidDatetimeFormatException extends \Params\Exception\ParamsException
{
    /**
     * Only strings are allowed datetime format.
     * @param int $index
     * @return InvalidDatetimeFormatException
     */
    public static function stringRequired(int $index)
    {
        $message = sprintf(
            Messages::ERROR_DATE_FORMAT_MUST_BE_STRING,
            gettype($index)
        );

        return new self($message);
    }
}
