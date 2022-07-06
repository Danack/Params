<?php

declare(strict_types = 1);

namespace Type\Exception;

use Type\Messages;

class InvalidRulesException extends \Type\Exception\ParamsException
{
    /**
     * Only strings and ints are allowed as array key.
     * @param mixed $badValue
     * @return InvalidRulesException
     */
    public static function badTypeForArrayAccess($badValue)
    {
        $message = sprintf(
            Messages::BAD_TYPE_FOR_ARRAY_ACCESS,
            gettype($badValue)
        );

        return new self($message);
    }


    public static function expectsStringForProcessing(string $classname): self
    {
        $message = sprintf(
            Messages::BAD_TYPE_FOR_STRING_PROCESS_RULE,
            $classname
        );

        return new self($message);
    }
}
