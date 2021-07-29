<?php

declare(strict_types = 1);

namespace ParamsTest\ProcessRule;

use Params\DataStorage\TestArrayDataStorage;
use Params\Messages;
use Params\OpenApi\OpenApiV300ParamDescription;
use Params\ProcessedValues;
use Params\ProcessRule\EarlierThanTime;
use ParamsTest\BaseTestCase;

/**
 * @coversNothing
 */
class EarlierThanTimeTest extends BaseTestCase
{

    /**
     * @covers \Params\ProcessRule\EarlierThanTime
     */
    public function testWorks()
    {
        $value = new \DateTime('2000-01-01');

        $processedValues = ProcessedValues::fromArray([]);
        $dataStorage = TestArrayDataStorage::fromSingleValue('newtime', $value);

        $compareTime = new \DateTime('2001-01-01');
        $rule = new EarlierThanTime($compareTime);
        $validationResult = $rule->process($value, $processedValues, $dataStorage);

        $this->assertNoProblems($validationResult);

        $this->assertSame($value, $validationResult->getValue());
        $this->assertFalse($validationResult->isFinalResult());
    }

    public function providesErrorsCorrectly()
    {
        yield ['2020-01-01', '2001-01-01'];
        yield ['2020-01-01 12:00:00', '2020-01-01 12:00:00'];
    }

    /**
     * @covers \Params\ProcessRule\EarlierThanTime
     * @dataProvider providesErrorsCorrectly
     */
    public function testErrorsCorrectly($input_time, $boundary_time)
    {
        $value = new \DateTime($input_time);

        $processedValues = ProcessedValues::fromArray([]);
        $dataStorage = TestArrayDataStorage::fromSingleValue('newtime', $value);

        $compareTime = new \DateTime($boundary_time);
        $rule = new EarlierThanTime($compareTime);
        $validationResult = $rule->process($value, $processedValues, $dataStorage);

        $this->assertValidationProblemRegexp(
            '/newtime',
            Messages::TIME_MUST_BE_BEFORE_TIME,
            $validationResult->getValidationProblems()
        );

        $this->assertCount(1, $validationResult->getValidationProblems());
        $this->assertTrue($validationResult->isFinalResult());
    }


    /**
     * @covers \Params\ProcessRule\EarlierThanTime
     */
    public function testPreviousTimeWrongType()
    {
        $value = new \StdClass();

        $processedValues = ProcessedValues::fromArray([]);
        $dataStorage = TestArrayDataStorage::fromSingleValue('newtime', $value);

        $compareTime = new \DateTime('2000-01-01');
        $rule = new EarlierThanTime($compareTime);
        $validationResult = $rule->process($value, $processedValues, $dataStorage);

        $this->assertValidationProblemRegexp(
            '/newtime',
            Messages::CURRENT_TIME_MUST_BE_DATETIMEINTERFACE,
            $validationResult->getValidationProblems()
        );

        $this->assertCount(1, $validationResult->getValidationProblems());
        $this->assertTrue($validationResult->isFinalResult());
    }



    /**
     * @covers \Params\ProcessRule\EarlierThanTime
     */
    public function testFormatting()
    {
        $compareTime = new \DateTime('2000-01-01');
        $rule = new EarlierThanTime($compareTime);

        $this->assertSame(
            $rule->getCompareTimeString(),
            $compareTime->format(\DateTime::RFC3339)
        );
    }

    /**
     * @covers \Params\ProcessRule\EarlierThanTime
     */
    public function testDescription()
    {
        $compareTime = new \DateTime('2000-01-01');

        $rule = new EarlierThanTime($compareTime);
        $description = $this->applyRuleToDescription($rule);

        $this->assertStringMatchesTemplateString(
            Messages::TIME_MUST_BE_BEFORE_TIME,
            $description->getDescription()
        );

        $this->assertStringContainsString(
            $rule->getCompareTimeString(),
            $description->getDescription()
        );
    }
}
