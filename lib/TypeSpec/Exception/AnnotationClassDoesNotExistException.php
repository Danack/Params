<?php

declare(strict_types=1);


namespace TypeSpec\Exception;

use TypeSpec\Messages;

class AnnotationClassDoesNotExistException extends ParamsException
{
    public static function create(
        string $classname,
        string $property_name,
        string $annotation_name
    ): self {
        $message = sprintf(
            Messages::PROPERTY_ANNOTATION_DOES_NOT_EXIST,
            $property_name,
            $classname,
            $annotation_name
        );

        return new self($message);
    }
}
