<?php

declare(strict_types=1);

namespace ParamsTest\ExtractRule;

use VarMap\ArrayVarMap;
use ParamsTest\BaseTestCase;
use Params\ExtractRule\GetOptionalBool;
use Params\ParamsValuesImpl;
use Params\Path;
use Params\DataLocator\SingleValueDataLocator;

/**
 * @coversNothing
 */
class GetOptionalBoolTest extends BaseTestCase
{
    public function provideTestCases()
    {
        // Test sane juggling works
        yield ['true', true];
        yield ['truuue', false];
        yield [null, false];
        yield [0, false];
        yield [1, true];
        yield [2, true];
        yield [-5000, true];

        // Test missing param is null
        // TODO test missing
//        yield [new ArrayVarMap([]), null];
    }

    /**
     * @covers \Params\ExtractRule\GetOptionalInt
     * @dataProvider provideTestCases
     */
    public function testValidation($input, $expectedValue)
    {
        $rule = new GetOptionalBool();
        $validator = new ParamsValuesImpl();
        $validationResult = $rule->process(
            Path::fromName('foo'),
            new ArrayVarMap([]),
            $validator,
            SingleValueDataLocator::create($input)
        );

        $this->assertEmpty($validationResult->getValidationProblems());
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }

    public function provideTestErrorCases()
    {
        // TODO - test exact error messages
        yield [fopen('php://memory', 'r+')]; // a stream is not a bool
        yield [[1, 2, 3]];  // an array is not a bool
        yield [new \StdClass()]; // A stdClass is not a bool
    }

    /**
     * @covers \Params\ExtractRule\GetOptionalBool
     * @dataProvider provideTestErrorCases
     */
    public function testBadInputErrors($inputValue)
    {
        $variableName = 'foo';
        $variables = [$variableName => $inputValue];

        $validator = new ParamsValuesImpl();
        $rule = new GetOptionalBool();
        $validationResult = $rule->process(
            Path::fromName($variableName),
            new ArrayVarMap($variables),
            $validator,
            SingleValueDataLocator::create($inputValue)
        );

        $this->assertExpectedValidationProblems($validationResult->getValidationProblems());
        $this->assertNull($validationResult->getValue());
    }
}
