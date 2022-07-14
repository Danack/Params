<?php

declare(strict_types=1);


namespace TypeSpec\Exception;

use TypeSpec\Messages;

class NoConstructorException extends TypeSpecException
{
    public static function noConstructor(string $classname): self
    {
        $message = sprintf(
            Messages::CLASS_LACKS_CONSTRUCTOR,
            $classname
        );

        return new self($message);
    }

    public static function notPublicConstructor(string $classname): self
    {
        $message = sprintf(
            Messages::CLASS_LACKS_PUBLIC_CONSTRUCTOR,
            $classname
        );

        return new self($message);
    }
}
