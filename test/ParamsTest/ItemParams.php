<?php

declare(strict_types=1);

namespace ParamsTest;

use Params\Rule\GetInt;
use Params\Rule\GetString;
use Params\Rule\MaxIntValue;
use Params\Rule\MinIntValue;
use Params\Rule\MinLength;
use Params\SafeAccess;
use VarMap\VarMap;

use Params\Rule\IntegerInput;

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

    public static function getRules(VarMap $variableMap)
    {
        return [
            'foo' => [
                new GetInt($variableMap),
                new MaxIntValue(100)
            ],
            'bar' => [
                new GetString($variableMap),
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
