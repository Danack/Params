<?php

declare(strict_types=1);


namespace TypeSpec\Exception;

use TypeSpec\Messages;

class MissingConstructorParameterNameException extends ParamsException
{
    public static function missingParam(string $classname, string $param_name): self
    {
        $message = sprintf(
            Messages::MISSING_PARAM_NAME,
            $classname,
            $param_name
        );

        return new self($message);
    }
}
