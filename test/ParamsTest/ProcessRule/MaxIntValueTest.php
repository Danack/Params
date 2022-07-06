<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Type\DataStorage\TestArrayDataStorage;
use Type\Messages;
use Type\OpenApi\OpenApiV300ParamDescription;
use ParamsTest\BaseTestCase;
use Type\ProcessRule\MaxIntValue;
use Type\ProcessedValues;

/**
 * @coversNothing
 */
class MaxIntValueTest extends BaseTestCase
{
    public function provideMaxIntCases()
    {
        $maxValue = 256;
        $underValue = $maxValue - 1;
        $exactValue = $maxValue ;
        $overValue = $maxValue + 1;

        return [
            [$maxValue, (string)$underValue],
            [$maxValue, (string)$exactValue],
            // TODO - think about these cases.
//            [$maxValue, 125.5, true],
//            [$maxValue, 'banana', true]
        ];
    }

    /**
     * @dataProvider provideMaxIntCases
     * @covers \Type\ProcessRule\MaxIntValue
     */
    public function testValidation(int $maxValue, string $inputValue)
    {
        $rule = new MaxIntValue($maxValue);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $inputValue,
            $processedValues,
            $dataStorage
        );


        $this->assertNoProblems($validationResult);
    }



    public function provideMaxIntErrors()
    {
        $maxValue = 256;
        $underValue = $maxValue - 1;
        $exactValue = $maxValue ;
        $overValue = $maxValue + 1;

        return [
            [$maxValue, (string)$overValue],

            // TODO - think about these cases.
//            [$maxValue, 125.5, true],
//            [$maxValue, 'banana', true]
        ];
    }

    /**
     * @dataProvider provideMaxIntErrors
     * @covers \Type\ProcessRule\MaxIntValue
     */
    public function testErrors(int $maxValue, string $inputValue)
    {
        $rule = new MaxIntValue($maxValue);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValue('foo', $inputValue);
        $validationResult = $rule->process(
            $inputValue,
            $processedValues,
            $dataStorage
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::INT_TOO_LARGE,
            $validationResult->getValidationProblems()
        );

        $this->assertOneErrorAndContainsString($validationResult, (string)$maxValue);
    }

    /**
     * @covers \Type\ProcessRule\MaxIntValue
     */
    public function testDescription()
    {
        $maxValue = 20;
        $rule = new MaxIntValue($maxValue);
        $description = $this->applyRuleToDescription($rule);

        $this->assertSame($maxValue, $description->getMaximum());
        $this->assertFalse($description->isExclusiveMaximum());
    }
}
