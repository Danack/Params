<?php

declare(strict_types = 1);

namespace ParamsTest\ProcessRule;

use Params\DataLocator\DataStorage;
use Params\Messages;
use Params\OpenApi\OpenApiV300ParamDescription;
use Params\ProcessedValues;
use Params\ProcessRule\LaterThanTime;
use ParamsTest\BaseTestCase;

/**
 * @coversNothing
 */
class LaterThanTimeTest extends BaseTestCase
{
    /**
     * @covers \Params\ProcessRule\LaterThanTime
     */
    public function testWorks()
    {
        $value = new \DateTime('2000-01-01');

        $processedValues = ProcessedValues::fromArray([]);
        $dataLocator = DataStorage::fromSingleValue('newtime', $value);

        $compareTime = new \DateTime('1999-01-01');
        $rule = new LaterThanTime($compareTime);
        $validationResult = $rule->process($value, $processedValues, $dataLocator);

        $this->assertNoProblems($validationResult);

        $this->assertSame($value, $validationResult->getValue());
        $this->assertFalse($validationResult->isFinalResult());
    }

    /**
     * @covers \Params\ProcessRule\LaterThanTime
     */
    public function testErrorsCorrectly()
    {
        $value = new \DateTime('2000-01-01');

        $processedValues = ProcessedValues::fromArray([]);
        $dataLocator = DataStorage::fromSingleValue('newtime', $value);

        $compareTime = new \DateTime('2001-01-01');
        $rule = new LaterThanTime($compareTime);
        $validationResult = $rule->process($value, $processedValues, $dataLocator);

        $this->assertCount(1, $validationResult->getValidationProblems());

        $this->assertValidationProblemRegexp(
            '/newtime',
            Messages::TIME_MUST_BE_AFTER_TIME,
            $validationResult->getValidationProblems()
        );
        $this->assertTrue($validationResult->isFinalResult());
    }


    /**
     * @covers \Params\ProcessRule\LaterThanTime
     */
    public function testPreviousTimeWrongType()
    {
        $value = new \StdClass();

        $processedValues = ProcessedValues::fromArray([]);
        $dataLocator = DataStorage::fromSingleValue('newtime', $value);

        $compareTime = new \DateTime('2000-01-01');
        $rule = new LaterThanTime($compareTime);
        $validationResult = $rule->process($value, $processedValues, $dataLocator);

        $this->assertValidationProblemRegexp(
            '/newtime',
            Messages::CURRENT_TIME_MUST_BE_DATETIMEINTERFACE,
            $validationResult->getValidationProblems()
        );

        $this->assertCount(1, $validationResult->getValidationProblems());
        $this->assertTrue($validationResult->isFinalResult());
    }



    /**
     * @covers \Params\ProcessRule\LaterThanTime
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
     * @covers \Params\ProcessRule\LaterThanTime
     */
    public function testDescription()
    {
        $description = new OpenApiV300ParamDescription('John');
        $compareTime = new \DateTime('2000-01-01');

        $rule = new LaterThanTime($compareTime);
        $rule->updateParamDescription($description);

        $this->assertStringRegExp(
            Messages::TIME_MUST_BE_AFTER_TIME,
            $description->getDescription()
        );

        $this->assertStringContainsString(
            $rule->getCompareTimeString(),
            $description->getDescription()
        );
    }
}
