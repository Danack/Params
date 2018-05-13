<?php

declare(strict_types=1);

namespace ParamsTest\Exception\Validator;

use Params\Rule\CheckSet;
use Params\Rule\CheckSetOrDefault;
use Params\Rule\SkipIfNull;
use ParamsTest\BaseTestCase;
use VarMap\ArrayVarMap;
use Params\Params;
use Params\Rule\AlwaysEndsRule;
use Params\Rule\MaxIntValue;

class ParamsTest extends BaseTestCase
{
    public function testMissingRuleThrows()
    {
        $rules = [
            'foo' => []
        ];

        $this->expectException(\Params\Exception\RulesEmptyException::class);
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


    public function testFinalResultStopsProcessing()
    {
        $finalValue = 123;

        $arrayVarMap = new ArrayVarMap(['foo' => 5]);
        $rules = [
            'foo' => [
                new CheckSet($arrayVarMap),
                // This rule will stop processing
                new AlwaysEndsRule($finalValue),
                // this rule would give an error if processing was not stopped.
                new MaxIntValue($finalValue - 5)
            ]
        ];

        $values = Params::validate($rules);
        $this->assertEquals($finalValue, $values[0]);
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
