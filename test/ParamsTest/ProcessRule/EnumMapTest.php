<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\DataLocator\DataStorage;
use Params\Messages;
use ParamsTest\BaseTestCase;
use Params\ProcessRule\EnumMap;
use Params\ProcessedValues;
use Params\Exception\InvalidRulesException;

/**
 * @coversNothing
 */
class EnumMapTest extends BaseTestCase
{

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
            DataStorage::fromSingleValue('foo', 'bar')
        );

        $problems = $validationResult->getValidationProblems();
        $this->assertCount(1, $problems);
        $firstProblem = $problems[0];

        $this->assertStringContainsString(
            'input1, input2',
            $firstProblem->getProblemMessage()
        );
        $this->assertSame(
            '/' . $name,
            $firstProblem->getDataLocator()->toString()
        );
    }

    public function provideTestWorks()
    {
        return [
            ['z', 'zoq'],
            ['number', '12345'],
        ];
    }


    /**
     * @dataProvider provideTestWorks
     * @covers \Params\ProcessRule\EnumMap
     */
    public function testWorks($testValue, $expectedValue)
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

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }



    public function provideTestErrors()
    {
        return [
            ['Zebranky '],
            [12345]
        ];
    }

    /**
     * @dataProvider provideTestErrors
     * @covers \Params\ProcessRule\EnumMap
     */
    public function testErrors($testValue)
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
            DataStorage::fromSingleValue('foo', $testValue)
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::ENUM_MAP_UKNOWN_VALUE,
            $validationResult->getValidationProblems()
        );

        $this->assertCount(1, $validationResult->getValidationProblems());
        $validationProblem = $validationResult->getValidationProblems()[0];

        foreach (array_keys($enumMap) as $key) {
            $this->assertStringContainsString($key, $validationProblem->getProblemMessage());
        }
    }

    /**
     * @covers \Params\ProcessRule\EnumMap
     */
    public function testBadValueThrows()
    {
        $enumMap = [
            'z' => 'zoq',
            'f' => 'fot',
            'p' => 'pik',
            'number' => '12345'
        ];

        $rule = new EnumMap($enumMap);
        $processedValues = new ProcessedValues();

        $this->expectException(InvalidRulesException::class);
        // TODO - this should break when PHP changes double => float.
        $this->expectErrorMessageMatches("/.*double.*/");

        $rule->process(
            5.0,
            $processedValues,
            DataStorage::fromArray([])
        );
    }


    /**
     * @covers \Params\ProcessRule\EnumMap
     */
    public function testDescription()
    {
        $enumMap = [
            'z' => 'zoq',
            'f' => 'fot',
            'p' => 'pik',
            'number' => '12345'
        ];

        $rule = new EnumMap($enumMap);
        $description = $this->applyRuleToDescription($rule);

        $this->assertSame(
            array_keys($enumMap),
            $description->getEnum()
        );
    }
}
