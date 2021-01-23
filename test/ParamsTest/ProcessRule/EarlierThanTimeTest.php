<?php

declare(strict_types = 1);

namespace ParamsTest\ProcessRule;

use Params\InputStorage\ArrayInputStorage;
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
        $dataLocator = ArrayInputStorage::fromSingleValue('newtime', $value);

        $compareTime = new \DateTime('2001-01-01');
        $rule = new EarlierThanTime($compareTime);
        $validationResult = $rule->process($value, $processedValues, $dataLocator);

        $this->assertNoProblems($validationResult);

        $this->assertSame($value, $validationResult->getValue());
        $this->assertFalse($validationResult->isFinalResult());
    }


    /**
     * @covers \Params\ProcessRule\EarlierThanTime
     */
    public function testErrorsCorrectly()
    {
        $value = new \DateTime('2020-01-01');

        $processedValues = ProcessedValues::fromArray([]);
        $dataLocator = ArrayInputStorage::fromSingleValue('newtime', $value);

        $compareTime = new \DateTime('2001-01-01');
        $rule = new EarlierThanTime($compareTime);
        $validationResult = $rule->process($value, $processedValues, $dataLocator);

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
        $dataLocator = ArrayInputStorage::fromSingleValue('newtime', $value);

        $compareTime = new \DateTime('2000-01-01');
        $rule = new EarlierThanTime($compareTime);
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
        $description = new OpenApiV300ParamDescription('John');
        $compareTime = new \DateTime('2000-01-01');

        $rule = new EarlierThanTime($compareTime);
        $rule->updateParamDescription($description);

        $this->assertStringRegExp(
            Messages::TIME_MUST_BE_BEFORE_TIME,
            $description->getDescription()
        );

        $this->assertStringContainsString(
            $rule->getCompareTimeString(),
            $description->getDescription()
        );
    }
}
