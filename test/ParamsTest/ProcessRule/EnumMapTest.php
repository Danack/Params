<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\DataLocator\DataStorage;
use Params\DataLocator\EmptyInputStorageAye;
use ParamsTest\BaseTestCase;
use Params\ProcessRule\EnumMap;
use Params\ProcessedValues;

/**
 * @coversNothing
 */
class EnumMapTest extends BaseTestCase
{
    public function provideTestCases()
    {
        return [
            ['z', false, 'zoq'],
            ['Zebranky ', true, null],
            ['number', false, '12345'],
            [12345, true, null]
        ];
    }


    public function testErrorMessage()
    {
        $enumMap = [
            'input1' => 'output1',
            'input2' => 'output2',
        ];
        $name = 'foo';

        $rule = new EnumMap($enumMap);
        $processedValues = new ProcessedValues();
        $validationResult = $rule->process(
            'unknown value',
            $processedValues,
            EmptyInputStorageAye::fromPath(['foo'])
        );

        $problems = $validationResult->getValidationProblems();
        $this->assertCount(1, $problems);
        $firstProblem = $problems[0];

//        $this->assertArrayHasKey(, $problemMessages, 'problem was not set for /foo');
        $this->assertStringContainsString(
            'input1, input2',
            $firstProblem->getProblemMessage()
        );
        $this->assertSame(
            '/' . $name,
            $firstProblem->getDataLocator()->toString()
        );
    }

    /**
     * @dataProvider provideTestCases
     * @covers \Params\ProcessRule\EnumMap
     */
    public function testValidation($testValue, $expectError, $expectedValue)
    {
        $enumMap = [
            'z' => 'zoq',
            'f' => 'fot',
            'p' => 'pik',
            'number' => '12345'
        ];

        $rule = new EnumMap($enumMap);
        $processedValues = new ProcessedValues();
        $validationResult = $rule->process(
            $testValue,
            $processedValues,
            DataStorage::fromArraySetFirstValue([$testValue])
        );

        if ($expectError) {
            $this->assertExpectedValidationProblems($validationResult->getValidationProblems());
            return;
        }

        $this->assertNoValidationProblems($validationResult->getValidationProblems());
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }
}
