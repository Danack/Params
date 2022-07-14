<?php

declare(strict_types = 1);

namespace TypeSpec\Exception;

use TypeSpec\Messages;

class MissingClassException extends TypeSpecException
{
    public static function fromClassname(string $classname): self
    {
        $message = sprintf(
            Messages::CLASS_NOT_FOUND,
            $classname
        );

        return new self($message);
    }
}
