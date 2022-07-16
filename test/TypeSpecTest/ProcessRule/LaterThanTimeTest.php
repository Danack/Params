<?php

declare(strict_types = 1);

namespace TypeSpecTest\ProcessRule;

use TypeSpec\DataStorage\TestArrayDataStorage;
use TypeSpec\Messages;
use TypeSpec\OpenApi\OpenApiV300ParamDescription;
use TypeSpec\ProcessedValues;
use TypeSpec\ProcessRule\LaterThanTime;
use TypeSpecTest\BaseTestCase;

/**
 * @coversNothing
 */
class LaterThanTimeTest extends BaseTestCase
{

    /**
     * @covers \TypeSpec\ProcessRule\LaterThanTime
     */
    public function testWorks()
    {
        $value = new \DateTime('2000-01-01');

        $processedValues = createProcessedValuesFromArray([]);
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('newtime', $value);

        $compareTime = new \DateTime('1999-01-01');
        $rule = new LaterThanTime($compareTime);
        $validationResult = $rule->process($value, $processedValues, $dataStorage);

        $this->assertNoProblems($validationResult);

        $this->assertSame($value, $validationResult->getValue());
        $this->assertFalse($validationResult->isFinalResult());
    }

    /**
     * @covers \TypeSpec\ProcessRule\LaterThanTime
     */
    public function testErrorsCorrectly()
    {
        $value = new \DateTime('2000-01-01');

        $processedValues = createProcessedValuesFromArray([]);
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('newtime', $value);

        $compareTime = new \DateTime('2001-01-01');
        $rule = new LaterThanTime($compareTime);
        $validationResult = $rule->process($value, $processedValues, $dataStorage);

        $this->assertCount(1, $validationResult->getValidationProblems());

        $this->assertValidationProblemRegexp(
            '/newtime',
            Messages::TIME_MUST_BE_AFTER_TIME,
            $validationResult->getValidationProblems()
        );
        $this->assertTrue($validationResult->isFinalResult());
    }

    /**
     * @covers \TypeSpec\ProcessRule\LaterThanTime
     */
    public function testSameTimeErrors()
    {
        $value = new \DateTime('2000-01-01 12:00:00');

        $processedValues = createProcessedValuesFromArray([]);
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('newtime', $value);

        $compareTime = new \DateTime('2000-01-01 12:00:00');
        $rule = new LaterThanTime($compareTime);
        $validationResult = $rule->process($value, $processedValues, $dataStorage);

        $this->assertValidationProblemRegexp(
            '/newtime',
            Messages::TIME_MUST_BE_AFTER_TIME,
            $validationResult->getValidationProblems()
        );
        $this->assertTrue($validationResult->isFinalResult());
    }


    /**
     * @covers \TypeSpec\ProcessRule\LaterThanTime
     */
    public function testPreviousTimeWrongType()
    {
        $value = new \StdClass();

        $processedValues = createProcessedValuesFromArray([]);
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('newtime', $value);

        $compareTime = new \DateTime('2000-01-01');
        $rule = new LaterThanTime($compareTime);
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
     * @covers \TypeSpec\ProcessRule\LaterThanTime
     */
    public function testFormatting()
    {
        $compareTime = new \DateTime('2000-01-01');
        $rule = new LaterThanTime($compareTime);

        $this->assertSame(
            $rule->getCompareTimeString(),
            $compareTime->format(\DateTime::RFC3339)
        );
    }

    /**
     * @covers \TypeSpec\ProcessRule\LaterThanTime
     */
    public function testDescription()
    {
        $compareTime = new \DateTime('2000-01-01');

        $rule = new LaterThanTime($compareTime);
        $description = $this->applyRuleToDescription($rule);

        $this->assertStringMatchesTemplateString(
            Messages::TIME_MUST_BE_AFTER_TIME,
            $description->getDescription()
        );

        $this->assertStringContainsString(
            $rule->getCompareTimeString(),
            $description->getDescription()
        );
    }
}
