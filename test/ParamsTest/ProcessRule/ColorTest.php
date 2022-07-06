<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Type\DataStorage\TestArrayDataStorage;
use Type\Messages;
use ParamsTest\BaseTestCase;
use Type\ProcessRule\PositiveInt;
use Type\ProcessedValues;
use Type\ProcessRule\IsRgbColor;

/**
 * @coversNothing
 */
class ColorTest extends BaseTestCase
{
    public function provideRgbColorWorks()
    {
        return [
            ['rgb(255, 255, 0)'],
            ['rgb(255,255,0)'],
        ];
    }

    /**
     * @dataProvider provideRgbColorWorks
     * @covers \Type\ProcessRule\IsRgbColor
     */
    public function testValidation($inputString)
    {
        $rule = new IsRgbColor();
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $inputString, $processedValues, $dataStorage
        );

        $this->assertNoProblems($validationResult);
    }

    public function provideRgbColorErrors()
    {
        return [
            ['rgb(255, 255, )', IsRgbColor::BAD_COLOR_STRING],
        ];
    }


    /**
     * @dataProvider provideRgbColorErrors
     * @covers \Type\ProcessRule\IsRgbColor
     */
    public function testErrors($testValue, $message)
    {
        $rule = new IsRgbColor();
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValue('foo', $testValue);
        $validationResult = $rule->process(
            $testValue, $processedValues, $dataStorage
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            $message,
            $validationResult->getValidationProblems()
        );
    }


    /**
     * @covers \Type\ProcessRule\IsRgbColor
     */
    public function testDescription()
    {
        $rule = new IsRgbColor();
        $description = $this->applyRuleToDescription($rule);

//        $this->assertSame(0, $description->getMinimum());
//        $this->assertFalse($description->getExclusiveMinimum());
    }
}
