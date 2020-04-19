<?php

declare(strict_types = 1);

namespace Params\Exception;

use Params\Messages;

class InvalidRulesException extends \Params\Exception\ParamsException
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
}
