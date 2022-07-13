<?php

declare(strict_types=1);

namespace TypeSpecTest\OpenApi;

use TypeSpec\ExtractRule\GetInt;
use TypeSpec\InputTypeSpec;
use TypeSpec\ProcessRule\MaxIntValue;
use TypeSpec\ProcessRule\MinIntValue;
use TypeSpec\SafeAccess;
use TypeSpec\Create\CreateFromVarMap;

class RequiredIntExample
{
    use SafeAccess;
    use CreateFromVarMap;

    const NAME = 'amount';

    const MIN = 10;

    const MAX = 100;

    public static function getInputParameterList()
    {
        return [
            new InputTypeSpec(
                self::NAME,
                new GetInt(),
                new MinIntValue(self::MIN),
                new MaxIntValue(self::MAX)
            ),
        ];
    }
}
