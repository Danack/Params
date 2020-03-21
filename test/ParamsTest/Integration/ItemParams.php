<?php

declare(strict_types=1);

namespace ParamsTest\Integration;

use Params\ExtractRule\GetInt;
use Params\ExtractRule\GetString;
use Params\InputToParamInfo;
use Params\ProcessRule\MaxIntValue;
use Params\ProcessRule\MinIntValue;
use Params\ProcessRule\MinLength;
use Params\SafeAccess;
use VarMap\VarMap;

use Params\ProcessRule\IntegerInput;

class ItemParams
{
    use SafeAccess;

    /** @var int  */
    private $foo;

    /** @var string */
    private $bar;

    /**
     *
     * @param int $foo
     * @param string $bar
     */
    public function __construct(int $foo, string $bar)
    {
        $this->foo = $foo;
        $this->bar = $bar;
    }

    public static function getInputToParamInfoList()
    {
        return [
            new InputToParamInfo(
                'foo',
                new GetInt(),
                new MaxIntValue(100)
            ),
            new InputToParamInfo(
                'bar',
                new GetString(),
                new MinLength(4)
            ),
        ];
    }

    /**
     * @return int
     */
    public function getFoo(): int
    {
        return $this->foo;
    }

    /**
     * @return string
     */
    public function getBar(): string
    {
        return $this->bar;
    }
}
