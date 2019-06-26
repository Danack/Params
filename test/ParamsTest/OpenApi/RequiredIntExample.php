<?php

declare(strict_types=1);

namespace ParamsTest\OpenApi;

use Params\FirstRule\GetInt;
use Params\SubsequentRule\MaxIntValue;
use Params\SubsequentRule\MaxLength;
use Params\SubsequentRule\MinIntValue;
use Params\SubsequentRule\MinLength;
use VarMap\VarMap;
use Params\FirstRule\GetStringOrDefault;
use Params\FirstRule\GetString;
use Params\SubsequentRule\Enum;
use Params\SafeAccess;
use Params\Create\CreateFromVarMap;

class RequiredIntExample
{
    use SafeAccess;
    use CreateFromVarMap;

    const NAME = 'amount';

    const MIN = 10;

    const MAX = 100;

    public static function getRules(VarMap $variableMap)
    {
        return [
            self::NAME => [
                new GetInt($variableMap),
                new MinIntValue(self::MIN),
                new MaxIntValue(self::MAX)
            ],
        ];
    }
}
