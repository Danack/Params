<?php

declare(strict_types = 1);

namespace TypeSpec\Exception;

use TypeSpec\Messages;
use TypeSpec\TypeSpec;

class TypeNotInputParameterListException extends TypeSpecException
{
    public static function fromClassname(string $classname): self
    {
        $message = sprintf(
            Messages::CLASS_MUST_IMPLEMENT_INPUT_PARAMETER,
            $classname,
            TypeSpec::class
        );

        return new self($message);
    }
}
