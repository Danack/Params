<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\DataLocator\DataStorage;
use Params\Messages;
use ParamsTest\BaseTestCase;
use Params\ProcessRule\PositiveInt;
use Params\ProcessedValues;

/**
 * @coversNothing
 */
class PositiveIntTest extends BaseTestCase
{
    public function provideTestCases()
    {
        return [
            ['5', 5, false],
            ['0', 0, false], // close enough
            [PositiveInt::MAX_SANE_VALUE, PositiveInt::MAX_SANE_VALUE, false],
            [PositiveInt::MAX_SANE_VALUE - 1, PositiveInt::MAX_SANE_VALUE - 1, false],
        ];
    }

    /**
     * @dataProvider provideTestCases
     * @covers \Params\ProcessRule\PositiveInt
     */
    public function testValidation($testValue, $expectedResult, $expectError)
    {
        $rule = new PositiveInt();
        $processedValues = new ProcessedValues();
        $dataLocator = DataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $testValue, $processedValues, $dataLocator
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), $expectedResult);
    }


    public function provideTestErrors()
    {
        return [
            ['5.5', Messages::INT_REQUIRED_FOUND_NON_DIGITS], // not an int
            ['banana', Messages::INT_REQUIRED_FOUND_NON_DIGITS], // not an int
            [PositiveInt::MAX_SANE_VALUE + 1 , Messages::INT_OVER_LIMIT],
        ];
    }

    /**
     * @dataProvider provideTestErrors
     * @covers \Params\ProcessRule\PositiveInt
     */
    public function testErrors($testValue, $message)
    {
        $rule = new PositiveInt();
        $processedValues = new ProcessedValues();
        $dataLocator = DataStorage::fromSingleValue('foo', $testValue);
        $validationResult = $rule->process(
            $testValue, $processedValues, $dataLocator
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            $message,
            $validationResult->getValidationProblems()
        );
    }


    /**
     * @covers \Params\ProcessRule\PositiveInt
     */
    public function testDescription()
    {
        $rule = new PositiveInt();
        $description = $this->applyRuleToDescription($rule);

        $this->assertSame(0, $description->getMinimum());
        $this->assertFalse($description->getExclusiveMinimum());
    }
}
