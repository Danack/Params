<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\DataLocator\DataStorage;
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
            [$maxValue, (string)$underValue, false],
            [$maxValue, (string)$exactValue, false],
            [$maxValue, (string)$overValue, true],

            // TODO - think about these cases.
//            [$maxValue, 125.5, true],
//            [$maxValue, 'banana', true]
        ];
    }

    /**
     * @dataProvider provideMaxIntCases
     * @covers \Params\ProcessRule\MaxIntValue
     */
    public function testValidation(int $maxValue, string $inputValue, bool $expectError)
    {
        $rule = new MaxIntValue($maxValue);
        $processedValues = new ProcessedValues();
        $dataLocator = DataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $inputValue,
            $processedValues,
            $dataLocator
        );

        if ($expectError === false) {
            $this->assertNoProblems($validationResult);
        }
        else {
            $this->assertExpectedValidationProblems($validationResult->getValidationProblems());
        }
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
