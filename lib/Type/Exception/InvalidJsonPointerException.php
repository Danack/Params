<?php

declare(strict_types = 1);

namespace Type\Exception;

use Type\Messages;

/**
 *
 */
class InvalidJsonPointerException extends ParamsException
{
    public static function invalidFirstCharacter(): self
    {
        return new self(Messages::INVALID_JSON_POINTER_FIRST);
    }
}
