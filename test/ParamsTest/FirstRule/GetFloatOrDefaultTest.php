<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use Params\FirstRule\GetFloatOrDefault;
use VarMap\ArrayVarMap;
use ParamsTest\BaseTestCase;
use Params\ParamsValidator;

/**
 * @coversNothing
 */
class GetFloatOrDefaultTest extends BaseTestCase
{
    public function provideTestCases()
    {
        return [
            // Test value is read as string
            [new ArrayVarMap(['foo' => '5']), 'john', 5.0],

            // Test value is read as float
            [new ArrayVarMap(['foo' => 5]), 'john', 5.0],

            // Test default is used as string
            [new ArrayVarMap([]), '5', 5.0],

            // Test default is used as float
            [new ArrayVarMap([]), 5, 5.0],

            // Test default is used as null
            [new ArrayVarMap([]), null, null],

            // Extra checks
            [new ArrayVarMap([]), '-1000.1', -1000.1],
        ];
    }

    /**
     * @covers \Params\FirstRule\GetIntOrDefault
     * @dataProvider provideTestCases
     */
    public function testValidation(ArrayVarMap $varMap, $default, $expectedValue)
    {
        $rule = new GetFloatOrDefault($default);
        $validator = new ParamsValidator();
        $validationResult = $rule->process('foo', $varMap, $validator);

        $this->assertEmpty($validationResult->getProblemMessages());
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }

    public function provideTestErrorCases()
    {
        return [
            [null],
            [''],
            ['6 apples'],
            ['banana'],
            ['1.f'],
        ];
    }

    /**
     * @covers \Params\FirstRule\GetIntOrDefault
     * @dataProvider provideTestErrorCases
     */
    public function testErrors($inputValue)
    {
        $default = 5;

        $variableName = 'foo';

        $variables = [$variableName => $inputValue];

        $validator = new ParamsValidator();
        $rule = new GetFloatOrDefault($default);
        $validationResult = $rule->process($variableName, new ArrayVarMap($variables), $validator);

        $this->assertNotNull($validationResult->getProblemMessages());
    }
}
