<?php

declare(strict_types=1);


namespace TypeSpec\Exception;

use TypeSpec\Messages;

class PropertyHasMultipleInputTypeSpecAnnotationsException extends TypeSpecException
{
    public static function create(string $classname, string $property_name): self
    {
        $message = sprintf(
            Messages::PROPERTY_MULTIPLE_INPUT_TYPE_SPEC,
            $property_name,
            $classname
        );

        return new self($message);
    }
}
