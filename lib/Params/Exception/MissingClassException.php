<?php

declare(strict_types = 1);

namespace Params\Exception;

class MissingClassException extends ParamsException
{
    public static function fromClassname(string $classname): self
    {
        $message = sprintf(
            "Class %s isn't available through auto-loader.",
            $classname
        );

        return new self($message);
    }
}
