<?php

declare(strict_types=1);

namespace ParamsTest\ExtractRule;

use Params\DataLocator\DataStorage;
use Params\ExtractRule\GetIntOrDefault;
use ParamsTest\BaseTestCase;
use Params\ProcessedValues;

/**
 * @coversNothing
 */
class GetIntOrDefaultTest extends BaseTestCase
{
    public function provideTestCases()
    {
        return [

//            // Test value is read as string
//            [new ArrayVarMap(['foo' => '5']), 'john', 5],
            // Test value is read as int
            [['foo' => 5], 20, 5],

//            // Test default is used as string
//            [new ArrayVarMap([]), '5', 5],

            // Test default is used as int
            [[], 5, 5],

            // Test default is used as null
            [[], null, null],
        ];
    }

    /**
     * @covers \Params\ExtractRule\GetIntOrDefault
     * @dataProvider provideTestCases
     */
    public function testValidation($data, $default, $expectedValue)
    {
        $rule = new GetIntOrDefault($default);
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
            ['1.1'],
        ];
    }

    /**
     * @covers \Params\ExtractRule\GetIntOrDefault
     * @dataProvider provideTestErrorCases
     */
    public function testErrors($inputValue)
    {
        $default = 5;

        $variableName = 'foo';

        $variables = [$variableName => $inputValue];

        $validator = new ProcessedValues();
        $rule = new GetIntOrDefault($default);
        $validationResult = $rule->process(
            $validator, DataStorage::fromArraySetFirstValue($variables)
        );

        $this->assertExpectedValidationProblems($validationResult->getValidationProblems());
    }
}
