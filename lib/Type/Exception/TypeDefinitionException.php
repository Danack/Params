<?php

declare(strict_types = 1);

namespace Type\Exception;

use Type\PropertyDefinition;
use Type\Messages;

class TypeDefinitionException extends ParamsException
{
    public static function foundNonPropertyDefinition(int $index, string $classname): self
    {
        $message = sprintf(
            Messages::MUST_RETURN_ARRAY_OF_PROPERTY_DEFINITION,
            $classname,
            PropertyDefinition::class,
            $index
        );

        return new self($message);
    }
}
