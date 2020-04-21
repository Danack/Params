<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\DataLocator\DataStorage;
use ParamsTest\BaseTestCase;
use Params\ProcessRule\PositiveInt;
use Params\ProcessedValues;

/**
 * @coversNothing
 */
class PositiveIntTest extends BaseTestCase
{
    public function provideTestCases()
    {
        return [
            ['5', 5, false],
            ['5.5', null, true], // not an int
            ['banana', null, true], // not an int
            ['0', 0, false], // close enough
            [PositiveInt::MAX_SANE_VALUE + 1 , null, true],
            [PositiveInt::MAX_SANE_VALUE, PositiveInt::MAX_SANE_VALUE, false],
            [PositiveInt::MAX_SANE_VALUE - 1, PositiveInt::MAX_SANE_VALUE - 1, false],
        ];
    }

    /**
     * @dataProvider provideTestCases
     * @covers \Params\ProcessRule\PositiveInt
     */
    public function testValidation($testValue, $expectedResult, $expectError)
    {
        $rule = new PositiveInt();
        $processedValues = new ProcessedValues();
        $dataLocator = DataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $testValue, $processedValues, $dataLocator
        );
        if ($expectError == true) {
            $this->assertExpectedValidationProblems($validationResult->getValidationProblems());
        }
        else {
            $this->assertNoProblems($validationResult);
            $this->assertEquals($validationResult->getValue(), $expectedResult);
        }
    }


    /**
     * @covers \Params\ProcessRule\PositiveInt
     */
    public function testDescription()
    {
        $rule = new PositiveInt();
        $description = $this->applyRuleToDescription($rule);

        $this->assertSame(0, $description->getMinimum());
        $this->assertFalse($description->getExclusiveMinimum());
    }
}
