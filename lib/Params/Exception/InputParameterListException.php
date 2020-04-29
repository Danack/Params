<?php

declare(strict_types = 1);

namespace Params\Exception;

use Params\InputParameter;
use Params\Messages;

class InputParameterListException extends ParamsException
{
    public static function foundNonInputParameter(int $index, string $classname): self
    {
        $message = sprintf(
            Messages::MUST_RETURN_ARRAY_OF__INPUT_PARAMETER,
            $classname,
            InputParameter::class,
            $index
        );

        return new self($message);
    }
}
