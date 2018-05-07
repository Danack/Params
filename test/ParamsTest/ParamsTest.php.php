<?php

declare(strict_types=1);

namespace ParamsTest\Exception\Validator;

use Params\Rule\CheckSet;
use Params\Rule\CheckSetOrDefault;
use Params\Rule\SkipIfNull;
use ParamsTest\BaseTestCase;
use VarMap\ArrayVarMap;
use Params\Params;

class ParamsExceptionTest extends BaseTestCase
{
    public function testMissingRuleThrows()
    {
        $rules = [
            'foo' => []
        ];

        $this->expectException(\Params\Exception\ParamsException::class);
        \Params\Params::validate($rules);
    }

    public function testInvalidInputThrows()
    {
        $arrayVarMap = new ArrayVarMap([]);

        $rules = [
            'foo' => [
                new CheckSet($arrayVarMap)
            ]
        ];

        $this->expectException(\Params\Exception\ValidationException::class);
        $this->expectExceptionMessage("Value not set for foo");
        Params::validate($rules);
    }

    public function testSkipOrNullCoverage()
    {
        $arrayVarMap = new ArrayVarMap([]);
        $rules = [
            'foo' => [
                new CheckSetOrDefault(null, $arrayVarMap),
                new SkipIfNull()
            ]
        ];

        list($foo) = Params::validate($rules);
        $this->assertNull($foo);
    }
}
