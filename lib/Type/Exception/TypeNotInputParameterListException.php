<?php

declare(strict_types = 1);

namespace Type\Exception;

use Type\Type;
use Type\Messages;

class TypeNotInputParameterListException extends ParamsException
{
    public static function fromClassname(string $classname): self
    {
        $message = sprintf(
            Messages::CLASS_MUST_IMPLEMENT_INPUT_PARAMETER,
            $classname,
            Type::class
        );

        return new self($message);
    }
}
