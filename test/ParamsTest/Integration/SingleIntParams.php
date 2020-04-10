<?php

declare(strict_types=1);

namespace ParamsTest\Integration;

use Params\ExtractRule\GetInt;
use Params\InputParameter;
use Params\ProcessRule\MaxIntValue;
use Params\ProcessRule\MinIntValue;
use Params\SafeAccess;
use VarMap\VarMap;
use Params\ProcessRule\IntegerInput;
use Params\InputParameterList;

class SingleIntParams implements InputParameterList
{
    use SafeAccess;

    /** @var int  */
    private $limit;

    public function __construct(int $limit)
    {
        $this->limit = $limit;
    }

    public static function getInputParameterList()
    {
        return [
            new InputParameter(
                'limit',
                new GetInt(),
                new IntegerInput(),
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
