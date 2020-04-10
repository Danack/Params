<?php

declare(strict_types=1);

namespace ParamsTest\ExtractRule;

use Params\DataLocator\DataStorage;
use Params\DataLocator\SingleValueInputStorageAye;
use VarMap\ArrayVarMap;
use ParamsTest\BaseTestCase;
use Params\ExtractRule\GetOptionalFloat;
use Params\ProcessedValuesImpl;
use Params\Path;

/**
 * @coversNothing
 */
class GetOptionalFloatTest extends BaseTestCase
{
    public function provideTestCases()
    {
        // Test value is read as string
        yield ['5', 5];

        // Testread as int
        yield [5, 5];

        yield ['5', 5];
        yield ['555555', 555555];
        yield ['1000.1', 1000.1];
        yield ['-1000.1', -1000.1];

        // Test missing param is null
        // TODO test missing as separate case.
//        yield [[], null];
    }

    /**
     * @covers \Params\ExtractRule\GetOptionalFloat
     * @dataProvider provideTestCases
     */
    public function testValidation($input, $expectedValue)
    {
        $rule = new GetOptionalFloat();
        $validator = new ProcessedValuesImpl();
        $validationResult = $rule->process(
            $validator,
            DataStorage::fromSingleValue('foo', $input)
        );

        $this->assertNoValidationProblems($validationResult->getValidationProblems());
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
        // $variableName = 'foo';
//        $variables = [$variableName => $inputValue];

        $validator = new ProcessedValuesImpl();
        $rule = new GetOptionalFloat();
        $validationResult = $rule->process(
            $validator,
            DataStorage::fromSingleValue('foo', $inputValue)
        );

        $this->assertExpectedValidationProblems($validationResult->getValidationProblems());
        $this->assertNull($validationResult->getValue());
    }
}
