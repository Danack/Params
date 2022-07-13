<?php

declare(strict_types=1);

namespace TypeSpecTest\ProcessRule;

use TypeSpec\DataStorage\TestArrayDataStorage;
use TypeSpec\OpenApi\OpenApiV300ParamDescription;
use TypeSpec\ProcessRule\CastToInt;
use TypeSpecTest\BaseTestCase;
use TypeSpec\ProcessedValues;
use TypeSpec\Messages;

/**
 * @coversNothing
 */
class IntegerInputTest extends BaseTestCase
{
    public function provideIntValueWorksCases()
    {
        return [
            ['5', 5],
            ['-10', -10],
            ['555555', 555555],
            [(string)CastToInt::MAX_SANE_VALUE, CastToInt::MAX_SANE_VALUE]
        ];
    }

    /**
     * @dataProvider provideIntValueWorksCases
     * @covers \TypeSpec\ProcessRule\CastToInt
     */
    public function testValidationWorks(string $inputValue, int $expectedValue)
    {
        $rule = new CastToInt();
        $processedValues = new ProcessedValues();
        $validationResult = $rule->process(
            $inputValue,
            $processedValues,
            TestArrayDataStorage::fromArraySetFirstValue([$inputValue])
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($expectedValue, $validationResult->getValue());
    }

    public function providesDetectsErrorsCorrectly()
    {
        return [
            // todo - we should test the exact error.
            [[], Messages::INT_REQUIRED_UNSUPPORTED_TYPE],
            ['5.0', Messages::INT_REQUIRED_FOUND_NON_DIGITS2],
            ['5.5', Messages::INT_REQUIRED_FOUND_NON_DIGITS2],
            ['banana', Messages::INT_REQUIRED_FOUND_NON_DIGITS2],
            ['', Messages::INT_REQUIRED_FOUND_EMPTY_STRING],
            [(string)(CastToInt::MAX_SANE_VALUE + 1), Messages::INTEGER_TOO_LONG]
        ];
    }

    /**
     * @dataProvider providesDetectsErrorsCorrectly
     * @covers \TypeSpec\ProcessRule\CastToInt
     */
    public function testDetectsErrorsCorrectly($inputValue, $message)
    {
        $rule = new CastToInt();
        $processedValues = new ProcessedValues();
        $validationResult = $rule->process(
            $inputValue,
            $processedValues,
            TestArrayDataStorage::fromSingleValue('foo', $inputValue)
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            $message,
            $validationResult->getValidationProblems()
        );
    }


    /**
     * @covers \TypeSpec\ProcessRule\CastToInt
     */
    public function testDescription()
    {
        $rule = new CastToInt();
        $description = $this->applyRuleToDescription($rule);
        $this->assertSame('integer', $description->getType());
    }
}
