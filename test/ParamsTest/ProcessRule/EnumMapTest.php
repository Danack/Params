<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\DataLocator\EmptyDataLocator;
use Params\DataLocator\SingleValueDataLocator;
use ParamsTest\BaseTestCase;
use Params\ProcessRule\EnumMap;
use Params\ParamsValuesImpl;
use Params\Path;
use function Params\createPath;

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
        $validator = new ParamsValuesImpl();
        $validationResult = $rule->process(
            Path::fromName($name),
            'unknown value',
            $validator,
            EmptyDataLocator::fromPath(['foo'])
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
            createPath(['name' => $name]),
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
        $validator = new ParamsValuesImpl();
        $validationResult = $rule->process(
            Path::fromName('foo'),
            $testValue,
            $validator,
            SingleValueDataLocator::create($testValue)
        );

        if ($expectError) {
            $this->assertExpectedValidationProblems($validationResult->getValidationProblems());
            return;
        }

        $this->assertEmpty($validationResult->getValidationProblems());
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }
}
