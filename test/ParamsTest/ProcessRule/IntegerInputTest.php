<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\DataLocator\DataStorage;
use Params\ProcessRule\IntegerInput;
use ParamsTest\BaseTestCase;
use Params\ProcessedValuesImpl;

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
        $processedValues = new ProcessedValuesImpl();
        $validationResult = $rule->process(
            $inputValue,
            $processedValues,
            DataStorage::fromArraySetFirstValue([$inputValue])
        );

        $this->assertNoValidationProblems($validationResult->getValidationProblems());
        $this->assertEquals($expectedValue, $validationResult->getValue());
    }

    public function providesDetectsErrorsCorrectly()
    {
        return [
            // todo - we should test the exact error.
            ['5.0'],
            ['5.5'],
            ['banana'],
            [''],
            [(string)(IntegerInput::MAX_SANE_VALUE + 1)]
        ];
    }

    /**
     * @dataProvider providesDetectsErrorsCorrectly
     * @covers \Params\ProcessRule\IntegerInput
     */
    public function testDetectsErrorsCorrectly(string $inputValue)
    {
        $rule = new IntegerInput();
        $processedValues = new ProcessedValuesImpl();
        $validationResult = $rule->process(
            $inputValue,
            $processedValues,
            DataStorage::fromArraySetFirstValue([$inputValue])
        );
        $this->assertExpectedValidationProblems($validationResult->getValidationProblems());
    }
}
