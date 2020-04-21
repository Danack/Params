<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\DataLocator\DataStorage;
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
            // todo - we should test the exact error.
            [[]],
            [''],
            ['5.a'],
            ['5.5 '], // trailing space
            [' 5.5'], // leading space
            ['5.5banana'], // trailing invalid chars
            ['banana'],
        ];
    }

    /**
     * @dataProvider provideErrorCases
     * @covers \Params\ProcessRule\FloatInput
     */
    public function testValidationErrors($inputValue)
    {
        $rule = new FloatInput();
        $processedValues = new ProcessedValues();
        $validationResult = $rule->process(
            $inputValue,
            $processedValues,
            DataStorage::fromArraySetFirstValue([$inputValue])
        );
        $this->assertExpectedValidationProblems($validationResult->getValidationProblems());
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
