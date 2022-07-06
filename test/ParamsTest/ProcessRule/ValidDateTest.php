<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Type\DataStorage\TestArrayDataStorage;
use Type\OpenApi\ParamDescription;
use Type\ProcessRule\ImagickIsRgbColor;
use ParamsTest\BaseTestCase;
use Type\ProcessRule\ValidDate;
use Type\ProcessedValues;
use Type\Messages;

/**
 * @coversNothing
 */
class ValidDateTest extends BaseTestCase
{
    public function provideTestWorksCases()
    {
        return [
            [
                '2002-10-02',
                \DateTime::createFromFormat('Y-m-d', '2002-10-02')->setTime(0, 0, 0, 0)
            ],
            [
                '2002-10-02',
                \DateTime::createFromFormat('Y-m-d', '2002-10-02')->setTime(0, 0, 0, 0)
            ],
        ];
    }


    /**
     * @dataProvider provideTestWorksCases
     * @covers \Type\ProcessRule\ValidDate
     */
    public function testValidationWorks($input, $expectedTime)
    {
        $rule = new ValidDate();
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $input, $processedValues, $dataStorage
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), $expectedTime);
    }

    public function provideTestErrorsCases()
    {
        return [
            ['2pm on Tuesday'],
            ['Banana'],
        ];
    }

    /**
     * @dataProvider provideTestErrorsCases
     * @covers \Type\ProcessRule\ValidDate
     */
    public function testValidationErrors($input)
    {
        $rule = new ValidDate();
        $processedValues = new ProcessedValues();
        $validationResult = $rule->process(
            $input,
            $processedValues,
            TestArrayDataStorage::fromSingleValue('foo', $input)
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::ERROR_INVALID_DATETIME,
            $validationResult->getValidationProblems()
        );
    }

    /**
     * @covers \Type\ProcessRule\ValidDate
     */
    public function testDescription()
    {
        $rule = new ValidDate();
        $description = $this->applyRuleToDescription($rule);

        $this->assertSame(ParamDescription::FORMAT_DATE, $description->getFormat());
        $this->assertSame(ParamDescription::TYPE_STRING, $description->getType());
    }
}
