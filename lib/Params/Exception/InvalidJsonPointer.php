<?php

declare(strict_types = 1);

namespace Params\Exception;

class InvalidJsonPointer extends ParamsException
{
    public static function invalidFirstCharacter(): self
    {
        return new self("First character must be /");
    }
}
