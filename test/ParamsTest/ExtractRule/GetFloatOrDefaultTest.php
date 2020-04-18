<?php

declare(strict_types=1);

namespace ParamsTest\ExtractRule;

use Params\DataLocator\DataStorage;
use Params\ExtractRule\GetFloatOrDefault;
use VarMap\ArrayVarMap;
use ParamsTest\BaseTestCase;
use Params\ProcessedValues;
use Params\Path;

/**
 * @coversNothing
 */
class GetFloatOrDefaultTest extends BaseTestCase
{
    public function provideTestCases()
    {
        return [
//            // Test value is read as string
//            [new ArrayVarMap(['foo' => '5']), 'john', 5.0],

            // Test value is read as float
            [['foo' => 5], 20, 5.0],

//            // Test default is used as string
//            [new ArrayVarMap([]), '5', 5.0],

            // Test default is used as float
            [[], 5, 5.0],

            // Test default is used as null
            [[], null, null],

            // Extra checks
            [[], -1000.1, -1000.1],
        ];
    }

    /**
     * @covers \Params\ExtractRule\GetIntOrDefault
     * @dataProvider provideTestCases
     */
    public function testValidation($data, $default, $expectedValue)
    {
        $rule = new GetFloatOrDefault($default);
        $validator = new ProcessedValues();

        $dataStorage = DataStorage::fromArray($data);
        $dataStorage = $dataStorage->moveKey('foo');

        $validationResult = $rule->process(
            $validator,
            $dataStorage
        );

        $this->assertNoValidationProblems($validationResult->getValidationProblems());
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }

    public function provideTestErrorCases()
    {
        return [
            [null],
            [''],
            ['6 apples'],
            ['banana'],
            ['1.f'],
        ];
    }

    /**
     * @covers \Params\ExtractRule\GetIntOrDefault
     * @dataProvider provideTestErrorCases
     */
    public function testErrors($inputValue)
    {
        $default = 5.0;

        $variableName = 'foo';

        $variables = [$variableName => $inputValue];

        $validator = new ProcessedValues();
        $rule = new GetFloatOrDefault($default);
        $validationResult = $rule->process(
            $validator, DataStorage::fromArraySetFirstValue($variables)
        );

        $this->assertExpectedValidationProblems($validationResult->getValidationProblems());
    }
}
