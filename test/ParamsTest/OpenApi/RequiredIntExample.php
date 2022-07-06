<?php

declare(strict_types=1);

namespace ParamsTest\OpenApi;

use Type\ExtractRule\GetInt;
use Type\PropertyDefinition;
use Type\ProcessRule\MaxIntValue;
use Type\ProcessRule\MinIntValue;
use Type\SafeAccess;
use Type\Create\CreateFromVarMap;

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
            new PropertyDefinition(
                self::NAME,
                new GetInt(),
                new MinIntValue(self::MIN),
                new MaxIntValue(self::MAX)
            ),
        ];
    }
}
