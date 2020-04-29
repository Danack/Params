<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\DataLocator\DataStorage;
use Params\Messages;
use Params\OpenApi\OpenApiV300ParamDescription;
use Params\ProcessRule\FloatInput;
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
     * @covers \Params\ProcessRule\FloatInput
     */
    public function testValidationWorks(string $inputValue, float $expectedValue)
    {
        $rule = new FloatInput();
        $processedValues = new ProcessedValues();
        $dataLocator = DataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $inputValue, $processedValues, $dataLocator
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($expectedValue, $validationResult->getValue());
    }

    public function provideErrorCases()
    {
        return [
            [[], Messages::VALUE_MUST_BE_SCALAR],
            [null, Messages::VALUE_MUST_BE_SCALAR],
            ['', Messages::NEED_FLOAT_NOT_EMPTY_STRING],
            ['5.a', Messages::NEED_FLOAT],
            ['5. 5', Messages::NEED_FLOAT_WHITESPACE], // space in middle
            ['5.5 ', Messages::NEED_FLOAT_WHITESPACE], // trailing space
            [' 5.5', Messages::NEED_FLOAT_WHITESPACE], // leading space
            ['5.5banana', Messages::NEED_FLOAT], // trailing invalid chars
            ['banana', Messages::NEED_FLOAT],
        ];
    }

    /**
     * @dataProvider provideErrorCases
     * @covers \Params\ProcessRule\FloatInput
     */
    public function testValidationErrors($inputValue, $message)
    {
        $rule = new FloatInput();
        $processedValues = new ProcessedValues();
        $validationResult = $rule->process(
            $inputValue,
            $processedValues,
            DataStorage::fromSingleValue('foo', $inputValue)
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            $message,
            $validationResult->getValidationProblems()
        );

    }

    /**
     * @covers \Params\ProcessRule\FloatInput
     */
    public function testDescription()
    {
        $description = new OpenApiV300ParamDescription('John');

        $rule = new FloatInput();
        $rule->updateParamDescription($description);
        $this->assertSame('float', $description->getType());
    }
}
