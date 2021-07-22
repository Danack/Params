<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\InputStorage\ArrayInputStorage;
use Params\Messages;
use Params\ProcessedValues;
use Params\ProcessRule\RgbColorRule;
use ParamsTest\BaseTestCase;
use Params\OpenApi\OpenApiV300ParamDescription;
use Params\Exception\InvalidRulesException;

/**
 * @coversNothing
 */
class RgbColorTest extends BaseTestCase
{

    public function provideTestWorks()
    {
        yield ['rgb(100,100,100)'];
        yield ['rgb(100, 100, 100)'];
    }

    /**
     * @dataProvider provideTestWorks
     * @covers \Params\ProcessRule\RgbColorRule
     */
    public function testWorks(string $testValue)
    {
        $rule = new RgbColorRule();
        $processedValues = new ProcessedValues();

        $dataLocator = ArrayInputStorage::fromSingleValue('foo', $testValue);

        $validationResult = $rule->process(
            $testValue, $processedValues, $dataLocator
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($testValue, $validationResult->getValue());
    }


    public function testOnlyString()
    {
        $testValue = 15;

        $rule = new RgbColorRule();
        $processedValues = new ProcessedValues();

        $dataLocator = ArrayInputStorage::fromSingleValue('foo', $testValue);

        $this->expectException(InvalidRulesException::class);
        $this->expectExceptionMessageMatchesTemplateString(
            \Params\Messages::BAD_TYPE_FOR_STRING_PROCESS_RULE
        );

        $rule->process(
            $testValue, $processedValues, $dataLocator
        );
    }


    public function provideTestErrors()
    {
        // TODO - these should give a precise position of the error.
        yield ['rgb("100,"100","100")'];
    }

    /**
     * @dataProvider provideTestErrors
     * @covers \Params\ProcessRule\RgbColorRule
     */
    public function testErrors($testValue)
    {
        $rule = new RgbColorRule();
        $processedValues = new ProcessedValues();
        $dataLocator = ArrayInputStorage::fromSingleValue('foo', $testValue);

        $validationResult = $rule->process(
            $testValue,
            $processedValues,
            $dataLocator
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            RgbColorRule::BAD_COLOR_STRING,
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
        $this->assertSame('color', $description->getFormat());
    }
}
