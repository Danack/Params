<?php

declare(strict_types=1);

namespace ParamsTest\OpenApi;

use Type\PropertyDefinition;
use Type\ProcessRule\MaxLength;
use Type\ProcessRule\MinLength;
use Type\ExtractRule\GetString;
use Type\SafeAccess;
use Type\Create\CreateFromVarMap;

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
            new PropertyDefinition(
                self::NAME,
                new GetString(),
                new MaxLength(self::MAX_LENGTH),
                new MinLength(self::MIN_LENGTH)
            ),
        ];
    }
}
