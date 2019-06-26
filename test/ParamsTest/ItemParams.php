<?php

declare(strict_types=1);

namespace ParamsTest;

use Params\FirstRule\GetInt;
use Params\FirstRule\GetString;
use Params\SubsequentRule\MaxIntValue;
use Params\SubsequentRule\MinIntValue;
use Params\SubsequentRule\MinLength;
use Params\SafeAccess;
use VarMap\VarMap;

use Params\SubsequentRule\IntegerInput;

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

    public static function getRules()
    {
        return [
            'foo' => [
                new GetInt(),
                new MaxIntValue(100)
            ],
            'bar' => [
                new GetString(),
                new MinLength(4),
            ],
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
