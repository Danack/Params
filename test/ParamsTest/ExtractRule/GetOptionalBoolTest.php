<?php

declare(strict_types=1);

namespace ParamsTest\ExtractRule;

use VarMap\ArrayVarMap;
use ParamsTest\BaseTestCase;
use Params\ExtractRule\GetOptionalBool;
use Params\ParamsValuesImpl;
use Params\Path;

/**
 * @coversNothing
 */
class GetOptionalBoolTest extends BaseTestCase
{
    public function provideTestCases()
    {
        // Test sane juggling works
        yield [new ArrayVarMap(['foo' => 'true']), true];
        yield [new ArrayVarMap(['foo' => 'truuue']), false];
        yield [new ArrayVarMap(['foo' => null]), false];
        yield [new ArrayVarMap(['foo' => 0]), false];
        yield [new ArrayVarMap(['foo' => 1]), true];
        yield [new ArrayVarMap(['foo' => 2]), true];
        yield [new ArrayVarMap(['foo' => -5000]), true];

        // Test missing param is null
        yield [new ArrayVarMap([]), null];
    }

    /**
     * @covers \Params\ExtractRule\GetOptionalInt
     * @dataProvider provideTestCases
     */
    public function testValidation(ArrayVarMap $varMap, $expectedValue)
    {
        $rule = new GetOptionalBool();
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
        // TODO - test exact error messages
        yield [fopen('php://memory', 'r+')];
        yield [[1, 2, 3]];
        yield [new \StdClass()];
    }

    /**
     * @covers \Params\ExtractRule\GetOptionalBool
     * @dataProvider provideTestErrorCases
     */
    public function testErrors($inputValue)
    {
        $variableName = 'foo';
        $variables = [$variableName => $inputValue];

        $validator = new ParamsValuesImpl();
        $rule = new GetOptionalBool();
        $validationResult = $rule->process(
            Path::fromName($variableName),
            new ArrayVarMap($variables),
            $validator
        );

        $this->assertExpectedValidationProblems($validationResult->getValidationProblems());
        $this->assertNull($validationResult->getValue());
    }
}
