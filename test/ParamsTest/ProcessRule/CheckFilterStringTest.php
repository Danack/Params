<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\DataLocator\DataStorage;
use Params\Value\MultipleEnums;
use ParamsTest\BaseTestCase;
use Params\ProcessRule\MultipleEnum;
use Params\ProcessedValues;

/**
 * @coversNothing
 */
class CheckFilterStringTest extends BaseTestCase
{
    public function providesKnownFilterCorrect()
    {
        return [
            ['foo', ['foo']],
            ['bar,foo', ['bar', 'foo']],
        ];
    }

    /**
     * @dataProvider providesKnownFilterCorrect
     * @covers \Params\ProcessRule\MultipleEnum
     */
    public function testKnownFilterCorrect($inputString, $expectedResult)
    {
        $rule = new MultipleEnum(['foo', 'bar']);
        $processedValues = new ProcessedValues();
        $dataLocator = DataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $inputString, $processedValues, $dataLocator
        );
        $this->assertNoProblems($validationResult);

        $validationValue = $validationResult->getValue();

        $this->assertInstanceOf(MultipleEnums::class, $validationValue);
        /** @var $validationValue \Params\Value\MultipleEnums */

        $this->assertEquals($expectedResult, $validationValue->getValues());
    }

    /**
     * @covers \Params\ProcessRule\MultipleEnum
     */
    public function testUnknownFilterErrors()
    {
        $expectedValue = 'zot';
        $rule = new MultipleEnum(['foo', 'bar']);
        $processedValues = new ProcessedValues();
        $validationResult = $rule->process(
            $expectedValue,
            $processedValues,
            DataStorage::fromArray(['foo', 'bar'])
        );
        $this->assertExpectedValidationProblems($validationResult->getValidationProblems());
    }
}
