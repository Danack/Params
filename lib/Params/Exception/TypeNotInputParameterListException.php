<?php

declare(strict_types = 1);

namespace Params\Exception;

use Params\InputParameterList;

class TypeNotInputParameterListException extends ParamsException
{
    public static function fromClassname(string $classname): self
    {
        $message = sprintf(
            "Class %s doesn't implement the %s interface. Cannot be used to get array of type.",
            $classname,
            InputParameterList::class
        );

        return new self($message);
    }
}
