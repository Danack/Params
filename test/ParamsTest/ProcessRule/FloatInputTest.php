<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\DataStorage\TestArrayDataStorage;
use Params\Messages;
use Params\OpenApi\OpenApiV300ParamDescription;
use Params\ProcessRule\CastToFloat;
use ParamsTest\BaseTestCase;
use Params\ProcessedValues;

/**
 * @coversNothing
 */
class FloatInputTest extends BaseTestCase
{
    public function provideWorksCases()
    {
        return [
            ['5', 5],
            ['555555', 555555],
            ['1000.1', 1000.1],
            ['-1000.1', -1000.1],
        ];
    }

    /**
     * @dataProvider provideWorksCases
     * @covers \Params\ProcessRule\CastToFloat
     */
    public function testValidationWorks(string $inputValue, float $expectedValue)
    {
        $rule = new CastToFloat();
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $inputValue, $processedValues, $dataStorage
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($expectedValue, $validationResult->getValue());
    }

    public function provideErrorCases()
    {
        return [
            [[], Messages::FLOAT_REQUIRED_WRONG_TYPE],
            [null, Messages::FLOAT_REQUIRED_WRONG_TYPE],
            ['', Messages::NEED_FLOAT_NOT_EMPTY_STRING],
            ['5.a', Messages::FLOAT_REQUIRED],
            ['5. 5', Messages::FLOAT_REQUIRED_FOUND_WHITESPACE], // space in middle
            ['5.5 ', Messages::FLOAT_REQUIRED_FOUND_WHITESPACE], // trailing space
            [' 5.5', Messages::FLOAT_REQUIRED_FOUND_WHITESPACE], // leading space
            ['5.5banana', Messages::FLOAT_REQUIRED], // trailing invalid chars
            ['banana', Messages::FLOAT_REQUIRED],
        ];
    }

    /**
     * @dataProvider provideErrorCases
     * @covers \Params\ProcessRule\CastToFloat
     */
    public function testValidationErrors($inputValue, $message)
    {
        $rule = new CastToFloat();
        $processedValues = new ProcessedValues();
        $validationResult = $rule->process(
            $inputValue,
            $processedValues,
            TestArrayDataStorage::fromSingleValue('foo', $inputValue)
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            $message,
            $validationResult->getValidationProblems()
        );
    }

    /**
     * @covers \Params\ProcessRule\CastToFloat
     */
    public function testDescription()
    {
        $rule = new CastToFloat();
        $description = $this->applyRuleToDescription($rule);
        $this->assertSame('float', $description->getType());
    }
}
