<?php

declare(strict_types=1);

namespace ParamsTest\OpenApi;

use Params\SubsequentRule\MaxLength;
use Params\SubsequentRule\MinLength;
use VarMap\VarMap;
use Params\FirstRule\GetStringOrDefault;
use Params\FirstRule\GetString;
use Params\SubsequentRule\Enum;
use Params\SafeAccess;
use Params\Create\CreateFromVarMap;

class RequiredStringExample
{
    use SafeAccess;
    use CreateFromVarMap;

    const NAME = 'status';

    const MIN_LENGTH = 10;

    const MAX_LENGTH = 100;

    public static function getRules()
    {
        return [
            self::NAME => [
                new GetString(),
                new MaxLength(self::MAX_LENGTH),
                new MinLength(self::MIN_LENGTH)
            ],
        ];
    }
}
