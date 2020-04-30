<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\DataLocator\DataStorage;
use Params\Messages;
use Params\OpenApi\OpenApiV300ParamDescription;
use ParamsTest\BaseTestCase;
use Params\ProcessRule\MaxIntValue;
use Params\ProcessedValues;

/**
 * @coversNothing
 */
class MaxIntValueValidatorTest extends BaseTestCase
{
    public function provideMaxIntCases()
    {
        $maxValue = 256;
        $underValue = $maxValue - 1;
        $exactValue = $maxValue ;
        $overValue = $maxValue + 1;

        return [
            [$maxValue, (string)$underValue],
            [$maxValue, (string)$exactValue],
            // TODO - think about these cases.
//            [$maxValue, 125.5, true],
//            [$maxValue, 'banana', true]
        ];
    }

    /**
     * @dataProvider provideMaxIntCases
     * @covers \Params\ProcessRule\MaxIntValue
     */
    public function testValidation(int $maxValue, string $inputValue)
    {
        $rule = new MaxIntValue($maxValue);
        $processedValues = new ProcessedValues();
        $dataLocator = DataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $inputValue,
            $processedValues,
            $dataLocator
        );


        $this->assertNoProblems($validationResult);
    }



    public function provideMaxIntErrors()
    {
        $maxValue = 256;
        $underValue = $maxValue - 1;
        $exactValue = $maxValue ;
        $overValue = $maxValue + 1;

        return [
            [$maxValue, (string)$overValue],

            // TODO - think about these cases.
//            [$maxValue, 125.5, true],
//            [$maxValue, 'banana', true]
        ];
    }

    /**
     * @dataProvider provideMaxIntErrors
     * @covers \Params\ProcessRule\MaxIntValue
     */
    public function testErrors(int $maxValue, string $inputValue)
    {
        $rule = new MaxIntValue($maxValue);
        $processedValues = new ProcessedValues();
        $dataLocator = DataStorage::fromSingleValue('foo', $inputValue);
        $validationResult = $rule->process(
            $inputValue,
            $processedValues,
            $dataLocator
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::INT_TOO_LARGE,
            $validationResult->getValidationProblems()
        );

        $this->assertOneErrorAndContainsString($validationResult, (string)$maxValue);
    }

    /**
     * @covers \Params\ProcessRule\MaxIntValue
     */
    public function testDescription()
    {
        $description = new OpenApiV300ParamDescription('John');
        $maxValue = 20;
        $rule = new MaxIntValue($maxValue);
        $rule->updateParamDescription($description);

        $this->assertSame($maxValue, $description->getMaximum());
        $this->assertFalse($description->isExclusiveMaximum());
    }
}
