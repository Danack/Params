<?php

declare(strict_types=1);

namespace ParamsTest\ExtractRule;

use VarMap\ArrayVarMap;
use ParamsTest\BaseTestCase;
use Params\ExtractRule\GetOptionalFloat;
use Params\ParamsValuesImpl;
use Params\Path;

/**
 * @coversNothing
 */
class GetOptionalFloatTest extends BaseTestCase
{
    public function provideTestCases()
    {
        // Test value is read as string
        yield [['foo' => '5'], 5];

        // Test value is read as int
        yield [['foo' => 5], 5];

        yield [['foo' => '5'], 5];
        yield [['foo' => '555555'], 555555];
        yield [['foo' => '1000.1'], 1000.1];
        yield [['foo' => '-1000.1'], -1000.1];

        // Test missing param is null
        yield [[], null];
    }

    /**
     * @covers \Params\ExtractRule\GetOptionalFloat
     * @dataProvider provideTestCases
     */
    public function testValidation($data, $expectedValue)
    {
        $rule = new GetOptionalFloat();
        $validator = new ParamsValuesImpl();
        $validationResult = $rule->process(
            Path::fromName('foo'),
            new ArrayVarMap($data),
            $validator
        );

        $this->assertEmpty($validationResult->getValidationProblems());
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }

    public function provideTestErrorCases()
    {
        // If set, null not allowed
        yield [null];

        // if set, empty string not allowed
        yield [''];
        yield ['6 apples'];
        yield ['banana'];
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
        $rule = new GetOptionalFloat();
        $validationResult = $rule->process(
            Path::fromName($variableName),
            new ArrayVarMap($variables),
            $validator
        );

        $this->assertExpectedValidationProblems($validationResult->getValidationProblems());
        $this->assertNull($validationResult->getValue());
    }
}
