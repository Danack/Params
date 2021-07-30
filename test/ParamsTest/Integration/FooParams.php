<?php

declare(strict_types=1);

namespace ParamsTest\Integration;

use Params\ExtractRule\GetInt;
use Params\InputParameter;
use Params\ProcessRule\MaxIntValue;
use Params\ProcessRule\MinIntValue;
use Params\SafeAccess;
use Params\InputParameterList;
use Params\ProcessRule\CastToInt;

class FooParams implements InputParameterList
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
    public static function getInputParameterList(): array
    {
        return [
            new InputParameter(
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
