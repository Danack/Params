<?php

declare(strict_types=1);


namespace Params\Exception;

use Params\Messages;

class NoConstructorException extends ParamsException
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
