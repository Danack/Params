<?php

declare(strict_types=1);

namespace ParamsTest\ExtractRule;

use Params\InputStorage\ArrayInputStorage;
use Params\Messages;
use ParamsTest\BaseTestCase;
use Params\ExtractRule\GetOptionalInt;
use Params\ProcessedValues;

/**
 * @coversNothing
 */
class GetOptionalIntTest extends BaseTestCase
{
    public function provideTestCases()
    {
        return [
            // Test value is read as string
            ['5', 5],
            // Test value is read as int
            [5, 5],

            // Test missing param is null
            // TODO - test missing separately
            //[new ArrayVarMap([]), null],
        ];
    }

    /**
     * @covers \Params\ExtractRule\GetOptionalInt
     * @dataProvider provideTestCases
     */
    public function testValidation($input, $expectedValue)
    {
        $rule = new GetOptionalInt();
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator,
            ArrayInputStorage::fromSingleValue('foo', $input)
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }

    public function provideTestErrorCases()
    {
        yield [null, Messages::INT_REQUIRED_UNSUPPORTED_TYPE];
        yield ['', Messages::INT_REQUIRED_FOUND_EMPTY_STRING];
        yield ['6 apples', Messages::INT_REQUIRED_FOUND_NON_DIGITS2];
        yield ['banana', Messages::INT_REQUIRED_FOUND_NON_DIGITS2];
        yield ['1.1', Messages::INT_REQUIRED_FOUND_NON_DIGITS2];
    }

    /**
     * @covers \Params\ExtractRule\GetOptionalInt
     * @dataProvider provideTestErrorCases
     */
    public function testErrors($inputValue, $message)
    {
        $validator = new ProcessedValues();
        $rule = new GetOptionalInt();
        $validationResult = $rule->process(
            $validator,
            ArrayInputStorage::fromSingleValue('foo', $inputValue)
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            $message,
            $validationResult->getValidationProblems()
        );

        $this->assertNull($validationResult->getValue());
    }

    /**
     * @covers \Params\ExtractRule\GetOptionalInt
     */
    public function testMissingGivesNull()
    {
        $rule = new GetOptionalInt();
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator, ArrayInputStorage::createMissing('foo')
        );

        $this->assertNoProblems($validationResult);
        $this->assertNull($validationResult->getValue());
    }


    /**
     * @covers \Params\ExtractRule\GetOptionalInt
     */
    public function testDescription()
    {
        $rule = new GetOptionalInt();
        $description = $this->applyRuleToDescription($rule);

        $rule->updateParamDescription($description);
        $this->assertSame('integer', $description->getType());
        $this->assertFalse($description->getRequired());
    }
}
