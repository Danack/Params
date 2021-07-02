<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\InputStorage\ArrayInputStorage;
use Params\Messages;
use ParamsTest\BaseTestCase;
use Params\ProcessRule\PositiveInt;
use Params\ProcessedValues;
use Params\ProcessRule\RgbColorRule;

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
     * @covers \Params\ProcessRule\RgbColorRule
     */
    public function testValidation($inputString)
    {
        $rule = new RgbColorRule();
        $processedValues = new ProcessedValues();
        $dataLocator = ArrayInputStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $inputString, $processedValues, $dataLocator
        );

        $this->assertNoProblems($validationResult);
    }

    public function provideRgbColorErrors()
    {
        return [
            ['rgb(255, 255, )', RgbColorRule::BAD_COLOR_STRING],
        ];
    }


    /**
     * @dataProvider provideRgbColorErrors
     * @covers \Params\ProcessRule\RgbColorRule
     */
    public function testErrors($testValue, $message)
    {
        $rule = new RgbColorRule();
        $processedValues = new ProcessedValues();
        $dataLocator = ArrayInputStorage::fromSingleValue('foo', $testValue);
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
     * @covers \Params\ProcessRule\RgbColorRule
     */
    public function testDescription()
    {
        $rule = new RgbColorRule();
        $description = $this->applyRuleToDescription($rule);

//        $this->assertSame(0, $description->getMinimum());
//        $this->assertFalse($description->getExclusiveMinimum());
    }
}
