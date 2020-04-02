<?php

declare(strict_types=1);

namespace ParamsTest\Integration;

use Params\Create\CreateFromVarMap;
use Params\Create\CreateArrayOfTypeFromArray;
use Params\ExtractRule\GetInt;
use Params\ExtractRule\GetString;
use Params\Param;
use Params\ProcessRule\MaxIntValue;
use Params\ProcessRule\MinLength;
use Params\SafeAccess;
use Params\InputParameterList;

class ItemParams implements InputParameterList
{
    use SafeAccess;
    use CreateFromVarMap;
    use CreateArrayOfTypeFromArray;

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

    /**
     * @return \Params\Param[]
     */
    public static function getInputParameterList()
    {
        return [
            new Param(
                'foo',
                new GetInt(),
                new MaxIntValue(100)
            ),
            new Param(
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
