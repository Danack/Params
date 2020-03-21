<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use Params\ExtractRule\GetIntOrDefault;
use VarMap\ArrayVarMap;
use ParamsTest\BaseTestCase;
use Params\ExtractRule\GetStringOrDefault;
use Params\ParamsValidator;

/**
 * @coversNothing
 */
class GetIntOrDefaultTest extends BaseTestCase
{
    public function provideTestCases()
    {
        return [

            // Test value is read as string
            [new ArrayVarMap(['foo' => '5']), 'john', 5],
            // Test value is read as int
            [new ArrayVarMap(['foo' => 5]), 'john', 5],

            // Test default is used as string
            [new ArrayVarMap([]), '5', 5],

            // Test default is used as int
            [new ArrayVarMap([]), 5, 5],

            // Test default is used as null
            [new ArrayVarMap([]), null, null],
        ];
    }

    /**
     * @covers \Params\ExtractRule\GetIntOrDefault
     * @dataProvider provideTestCases
     */
    public function testValidation(ArrayVarMap $varMap, $default, $expectedValue)
    {
        $rule = new GetIntOrDefault($default);
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
            ['1.1'],
        ];
    }

    /**
     * @covers \Params\ExtractRule\GetIntOrDefault
     * @dataProvider provideTestErrorCases
     */
    public function testErrors($inputValue)
    {
        $default = 5;

        $variableName = 'foo';

        $variables = [$variableName => $inputValue];

        $validator = new ParamsValidator();
        $rule = new GetIntOrDefault($default);
        $validationResult = $rule->process($variableName, new ArrayVarMap($variables), $validator);

        $this->assertNotNull($validationResult->getProblemMessages());
    }
}
