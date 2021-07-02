<?php

declare(strict_types=1);


namespace Params\Exception;

use Params\Messages;

class PropertyHasMultipleParamAnnotationsException extends ParamsException
{
    public static function create(string $classname, string $property_name): self
    {
        $message = sprintf(
            Messages::PROPERTY_MULTIPLE_PARAMS,
            $property_name,
            $classname
        );

        return new self($message);
    }
}
