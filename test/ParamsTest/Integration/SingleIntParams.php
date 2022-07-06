<?php

declare(strict_types=1);

namespace ParamsTest\Integration;

use Type\ExtractRule\GetInt;
use Type\PropertyDefinition;
use Type\ProcessRule\MaxIntValue;
use Type\ProcessRule\MinIntValue;
use Type\SafeAccess;
use Type\ProcessRule\CastToInt;
use Type\Type;

class SingleIntParams implements Type
{
    use SafeAccess;

    private int $limit;

    public function __construct(int $limit)
    {
        $this->limit = $limit;
    }

    public static function getPropertyDefinitionList(): array
    {
        return [
            new PropertyDefinition(
                'limit',
                new GetInt(),
                new CastToInt(),
                new MinIntValue(0),
                new MaxIntValue(100)
            )
        ];
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }
}
