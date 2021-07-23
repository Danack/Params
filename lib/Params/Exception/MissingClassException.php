<?php

declare(strict_types = 1);

namespace Params\Exception;

use Params\Messages;

class MissingClassException extends ParamsException
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
