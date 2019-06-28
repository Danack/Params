<?php

declare(strict_types=1);

namespace ParamsTest\Integration;

use Params\FirstRule\GetInt;
use Params\SubsequentRule\MaxIntValue;
use Params\SubsequentRule\MinIntValue;
use Params\SafeAccess;
use VarMap\VarMap;

use Params\SubsequentRule\IntegerInput;

class SingleIntParams
{
    use SafeAccess;

    /** @var int  */
    private $limit;

    public function __construct(int $limit)
    {
        $this->limit = $limit;
    }

    public static function getRules()
    {
        return [
            'limit' => [
                new GetInt(),
                new IntegerInput(),
                new MinIntValue(0),
                new MaxIntValue(100)
            ]
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
