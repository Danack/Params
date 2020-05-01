<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\DataLocator\DataStorage;
use Params\OpenApi\OpenApiV300ParamDescription;
use Params\ProcessRule\IntegerInput;
use ParamsTest\BaseTestCase;
use Params\ProcessedValues;
use Params\Messages;

/**
 * @coversNothing
 */
class IntegerInputTest extends BaseTestCase
{
    public function provideIntValueWorksCases()
    {
        return [
            ['5', 5],
            ['-10', -10],
            ['555555', 555555],
            [(string)IntegerInput::MAX_SANE_VALUE, IntegerInput::MAX_SANE_VALUE]
        ];
    }

    /**
     * @dataProvider provideIntValueWorksCases
     * @covers \Params\ProcessRule\IntegerInput
     */
    public function testValidationWorks(string $inputValue, int $expectedValue)
    {
        $rule = new IntegerInput();
        $processedValues = new ProcessedValues();
        $validationResult = $rule->process(
            $inputValue,
            $processedValues,
            DataStorage::fromArraySetFirstValue([$inputValue])
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($expectedValue, $validationResult->getValue());
    }

    public function providesDetectsErrorsCorrectly()
    {
        return [
            // todo - we should test the exact error.
            ['5.0', Messages::INT_REQUIRED_FOUND_NON_DIGITS2],
            ['5.5', Messages::INT_REQUIRED_FOUND_NON_DIGITS2],
            ['banana', Messages::INT_REQUIRED_FOUND_NON_DIGITS2],
            ['', Messages::INT_REQUIRED_FOUND_EMPTY_STRING],
            [(string)(IntegerInput::MAX_SANE_VALUE + 1), Messages::INTEGER_TOO_LONG]
        ];
    }

    /**
     * @dataProvider providesDetectsErrorsCorrectly
     * @covers \Params\ProcessRule\IntegerInput
     */
    public function testDetectsErrorsCorrectly(string $inputValue, $message)
    {
        $rule = new IntegerInput();
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
     * @covers \Params\ProcessRule\IntegerInput
     */
    public function testDescription()
    {
        $description = new OpenApiV300ParamDescription('John');

        $rule = new IntegerInput();
        $rule->updateParamDescription($description);
        $this->assertSame('integer', $description->getType());
    }
}
