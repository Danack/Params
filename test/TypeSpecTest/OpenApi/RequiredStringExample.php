<?php

declare(strict_types=1);

namespace TypeSpecTest\OpenApi;

use TypeSpec\InputTypeSpec;
use TypeSpec\ProcessRule\MaxLength;
use TypeSpec\ProcessRule\MinLength;
use TypeSpec\ExtractRule\GetString;
use TypeSpec\SafeAccess;
use TypeSpec\Create\CreateFromVarMap;

class RequiredStringExample
{
    use SafeAccess;
    use CreateFromVarMap;

    const NAME = 'status';

    const MIN_LENGTH = 10;

    const MAX_LENGTH = 100;

    public static function getInputParameterList()
    {
        return [
            new InputTypeSpec(
                self::NAME,
                new GetString(),
                new MaxLength(self::MAX_LENGTH),
                new MinLength(self::MIN_LENGTH)
            ),
        ];
    }
}
