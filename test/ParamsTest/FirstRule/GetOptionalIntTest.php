<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use VarMap\ArrayVarMap;
use ParamsTest\BaseTestCase;
use Params\ExtractRule\GetOptionalInt;
use Params\ParamsValuesImpl;

/**
 * @coversNothing
 */
class GetOptionalIntTest extends BaseTestCase
{
    public function provideTestCases()
    {
        return [
            // Test value is read as string
            [new ArrayVarMap(['foo' => '5']), 5],
            // Test value is read as int
            [new ArrayVarMap(['foo' => 5]), 5],

            // Test missing param is null
            [new ArrayVarMap([]), null],
        ];
    }

    /**
     * @covers \Params\ExtractRule\GetOptionalInt
     * @dataProvider provideTestCases
     */
    public function testValidation(ArrayVarMap $varMap, $expectedValue)
    {
        $rule = new GetOptionalInt();
        $validator = new ParamsValuesImpl();
        $validationResult = $rule->process('foo', $varMap, $validator);

        $this->assertEmpty($validationResult->getValidationProblems());
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }

    public function provideTestErrorCases()
    {
        return [
            [''],
            ['6 apples'],
            ['banana'],
            ['1.1'],
        ];
    }

    /**
     * @covers \Params\ExtractRule\GetOptionalInt
     * @dataProvider provideTestErrorCases
     */
    public function testErrors($inputValue)
    {
        $variableName = 'foo';
        $variables = [$variableName => $inputValue];

        $validator = new ParamsValuesImpl();
        $rule = new GetOptionalInt();
        $validationResult = $rule->process($variableName, new ArrayVarMap($variables), $validator);

        $this->assertNotNull($validationResult->getValidationProblems());
        $this->assertNull($validationResult->getValue());
    }
}
