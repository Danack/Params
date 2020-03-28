<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use Params\ExtractRule\GetFloatOrDefault;
use VarMap\ArrayVarMap;
use ParamsTest\BaseTestCase;
use Params\ParamsValuesImpl;
use Params\Path;

/**
 * @coversNothing
 */
class GetFloatOrDefaultTest extends BaseTestCase
{
    public function provideTestCases()
    {
        return [
//            // Test value is read as string
//            [new ArrayVarMap(['foo' => '5']), 'john', 5.0],

            // Test value is read as float
            [new ArrayVarMap(['foo' => 5]), 20, 5.0],

//            // Test default is used as string
//            [new ArrayVarMap([]), '5', 5.0],

            // Test default is used as float
            [new ArrayVarMap([]), 5, 5.0],

            // Test default is used as null
            [new ArrayVarMap([]), null, null],

            // Extra checks
            [new ArrayVarMap([]), -1000.1, -1000.1],
        ];
    }

    /**
     * @covers \Params\ExtractRule\GetIntOrDefault
     * @dataProvider provideTestCases
     */
    public function testValidation(ArrayVarMap $varMap, $default, $expectedValue)
    {
        $rule = new GetFloatOrDefault($default);
        $validator = new ParamsValuesImpl();
        $validationResult = $rule->process(
            Path::fromName('foo'),
            $varMap,
            $validator
        );

        $this->assertEmpty($validationResult->getValidationProblems());
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
     * @covers \Params\ExtractRule\GetIntOrDefault
     * @dataProvider provideTestErrorCases
     */
    public function testErrors($inputValue)
    {
        $default = 5;

        $variableName = 'foo';

        $variables = [$variableName => $inputValue];

        $validator = new ParamsValuesImpl();
        $rule = new GetFloatOrDefault($default);
        $validationResult = $rule->process(
            Path::fromName($variableName),
            new ArrayVarMap($variables),
            $validator
        );

        $this->assertNotNull($validationResult->getValidationProblems());
    }
}
