<?php

declare(strict_types=1);

namespace ParamsTest\Integration;

use Type\ExtractRule\GetInt;
use Type\PropertyDefinition;
use Type\ProcessRule\MaxIntValue;
use Type\ProcessRule\MinIntValue;
use Type\SafeAccess;
use Type\Type;
use Type\ProcessRule\CastToInt;

class FooParams implements Type
{
    use SafeAccess;

    /** @var int  */
    private $limit;

    public function __construct(int $limit)
    {
        $this->limit = $limit;
    }

    /**
     * @return array
     */
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
