<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\DataLocator\DataStorage;
use ParamsTest\BaseTestCase;
use Params\ProcessRule\RangeIntValue;
use Params\ProcessedValues;

/**
 * @coversNothing
 */
class RangeIntValueTest extends BaseTestCase
{
    public function provideMinIntValueCases()
    {
        $minValue = 100;
        $maxValue = 200;
        $underValue = $minValue - 1;
        $exactValue = $minValue ;
        $overValue = $minValue + 1;

        return [
            [$minValue, $maxValue, (string)$underValue, true],
            [$minValue, $maxValue, (string)$exactValue, false],
            [$minValue, $maxValue, (string)$overValue, false],

//            // TODO - think about these cases.
//            [$minValue, 'banana', true]
        ];
    }

    public function provideMaxIntCases()
    {
        $minValue = 100;
        $maxValue = 256;
        $underValue = $maxValue - 1;
        $exactValue = $maxValue ;
        $overValue = $maxValue + 1;

        return [
            [$minValue, $maxValue, (string)$underValue, false],
            [$minValue, $maxValue, (string)$exactValue, false],
            [$minValue, $maxValue, (string)$overValue, true],

            // TODO - think about these cases.
//            [$maxValue, 125.5, true],
//            [$maxValue, 'banana', true]
        ];
    }

    public function provideRangeIntValueCases()
    {
        yield from $this->provideMinIntValueCases();
        yield from $this->provideMaxIntCases();
    }

    /**
     * @dataProvider provideRangeIntValueCases
     * @covers \Params\ProcessRule\RangeIntValue
     */
    public function testValidation(int $minValue, int $maxValue, string $inputValue, bool $expectError)
    {
        $rule = new RangeIntValue($minValue, $maxValue);
        $processedValues = new ProcessedValues();
        $dataLocator = DataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $inputValue, $processedValues, $dataLocator
        );

        if ($expectError === false) {
            $this->assertNoProblems($validationResult);
        }
        else {
            $this->assertExpectedValidationProblems($validationResult->getValidationProblems());
        }
    }


    /**
     * @covers \Params\ProcessRule\RangeIntValue
     */
    public function testDescription()
    {
        $rule = new RangeIntValue(10, 20);
        $description = $this->applyRuleToDescription($rule);

        $this->assertSame(10, $description->getMinimum());
        $this->assertFalse($description->getExclusiveMinimum());

        $this->assertSame(20, $description->getMaximum());
        $this->assertFalse($description->getExclusiveMaximum());
    }
}
