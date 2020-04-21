<?php

declare(strict_types=1);

namespace ParamsTest\ExtractRule;

use Params\DataLocator\DataStorage;
use ParamsTest\BaseTestCase;
use Params\ExtractRule\GetOptionalBool;
use Params\ProcessedValues;

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
     * @covers \Params\ExtractRule\GetOptionalBool
     * @dataProvider provideTestCases
     */
    public function testValidation($input, $expectedValue)
    {
        $rule = new GetOptionalBool();
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator, DataStorage::fromSingleValue('foo', $input)
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }


    /**
     * @covers \Params\ExtractRule\GetOptionalBool
     */
    public function testMissingGivesNull()
    {
        $rule = new GetOptionalBool();
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator, DataStorage::createMissing('foo')
        );

        $this->assertNoProblems($validationResult);
        $this->assertNull($validationResult->getValue());
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
        $validator = new ProcessedValues();
        $rule = new GetOptionalBool();
        $validationResult = $rule->process(
            $validator, DataStorage::fromSingleValue('foo', $inputValue)
        );

        $this->assertExpectedValidationProblems($validationResult->getValidationProblems());
        $this->assertNull($validationResult->getValue());
    }

    /**
     * @covers \Params\ExtractRule\GetOptionalBool
     */
    public function testDescription()
    {
        $rule = new GetOptionalBool();
        $description = $this->applyRuleToDescription($rule);

        $rule->updateParamDescription($description);
        $this->assertSame('boolean', $description->getType());
        $this->assertFalse($description->getRequired());
    }
}
