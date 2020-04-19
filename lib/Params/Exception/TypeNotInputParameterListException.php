<?php

declare(strict_types = 1);

namespace Params\Exception;

use Params\InputParameterList;
use Params\Messages;

class TypeNotInputParameterListException extends ParamsException
{
    public static function fromClassname(string $classname): self
    {
        $message = sprintf(
            Messages::CLASS_MUST_IMPLEMENT_INPUT_PARAMETER,
            $classname,
            InputParameterList::class
        );

        return new self($message);
    }
}
