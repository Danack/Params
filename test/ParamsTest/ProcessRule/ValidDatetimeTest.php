<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\DataStorage\TestArrayDataStorage;
use Params\Messages;
use Params\OpenApi\ParamDescription;
use Params\ProcessRule\ValidDate;
use ParamsTest\BaseTestCase;
use Params\ProcessRule\ValidDatetime;
use Params\ProcessedValues;

/**
 * @coversNothing
 */
class ValidDatetimeTest extends BaseTestCase
{
    public function provideTestWorksCases()
    {
        return [
            [
                '2002-10-02T10:00:00-05:00',
                \DateTime::createFromFormat(\DateTime::RFC3339, '2002-10-02T10:00:00-05:00')
            ],
            [
                '2002-10-02T15:00:00Z',
                \DateTime::createFromFormat(\DateTime::RFC3339, '2002-10-02T15:00:00Z')
            ],

// This should work - but currently doesn't due to https://bugs.php.net/bug.php?id=75577
//            [
//                '2017-07-25T13:47:12.000+00:00',
//                \DateTime::createFromFormat(\DateTime::RFC3339_EXTENDED, '2017-07-25T13:47:12.000+00:00')
//            ],


            // TODO - this should be an allowed string - I think
            // '2002-10-02T15:00:00.05Z'

            // TODO - add support for 6 digit microseconds e.g. formatted by Golang
            // "Y-m-d\TH:i:s.uP", "2017-07-25T15:25:16.123456+02:00"
        ];
    }


    /**
     * @dataProvider provideTestWorksCases
     * @covers \Params\ProcessRule\ValidDatetime
     */
    public function testValidationWorks($input, $expectedTime)
    {
        $rule = new ValidDatetime();
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
     * @covers \Params\ProcessRule\ValidDatetime
     */
    public function testValidationErrors($input)
    {
        $rule = new ValidDatetime();
        $processedValues = new ProcessedValues();

        $dataStorage = TestArrayDataStorage::fromSingleValue('foo', $input);

        $validationResult = $rule->process(
            $input, $processedValues, $dataStorage
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::ERROR_INVALID_DATETIME,
            $validationResult->getValidationProblems()
        );
    }

    /**
     * @covers \Params\ProcessRule\ValidDatetime
     */
    public function testDescription()
    {
        $rule = new ValidDatetime();
        $description = $this->applyRuleToDescription($rule);

        $this->assertSame(ParamDescription::FORMAT_DATETIME, $description->getFormat());
        $this->assertSame(ParamDescription::TYPE_STRING, $description->getType());
    }
}
