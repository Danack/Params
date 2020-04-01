<?php

declare(strict_types = 1);

namespace Params\Exception;

use Params\ParamAware;

class BadTypeException extends ParamsException
{
    public static function fromClassname(string $classname): self
    {
        $message = sprintf(
            'Type %s does not implement %s which is required.',
            $classname,
            ParamAware::class
        );

        return new self($message);
    }
}
