<?php

declare(strict_types=1);

namespace ParamsTest\ExtractRule;

use Params\DataLocator\DataStorage;
use Params\ExtractRule\GetFloatOrDefault;
use ParamsTest\BaseTestCase;
use Params\ProcessedValues;

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
     * @covers \Params\ExtractRule\GetFloatOrDefault
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

        $this->assertNoProblems($validationResult);
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
     * @covers \Params\ExtractRule\GetFloatOrDefault
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


    /**
     * @covers \Params\ExtractRule\GetFloatOrDefault
     */
    public function testDescription()
    {
        $rule = new GetFloatOrDefault(4.5);
        $description = $this->applyRuleToDescription($rule);

        $rule->updateParamDescription($description);
        $this->assertSame('number', $description->getType());
        $this->assertFalse($description->getRequired());
        $this->assertSame(4.5, $description->getDefault());
    }
}
