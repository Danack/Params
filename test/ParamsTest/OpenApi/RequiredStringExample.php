<?php

declare(strict_types=1);

namespace ParamsTest\OpenApi;

use Params\InputParameter;
use Params\ProcessRule\MaxLength;
use Params\ProcessRule\MinLength;
use Params\ExtractRule\GetString;
use Params\SafeAccess;
use Params\Create\CreateFromVarMap;

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
            new InputParameter(
                self::NAME,
                new GetString(),
                new MaxLength(self::MAX_LENGTH),
                new MinLength(self::MIN_LENGTH)
            ),
        ];
    }
}
