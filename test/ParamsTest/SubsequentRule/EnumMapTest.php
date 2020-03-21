<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use ParamsTest\BaseTestCase;
use Params\ProcessRule\EnumMap;
use Params\ParamsValidator;

/**
 * @coversNothing
 */
class KnownEnumValidatorTest extends BaseTestCase
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

        $rule = new EnumMap($enumMap);
        $validator = new ParamsValidator();
        $validationResult = $rule->process('foo', 'unknown value', $validator);


        $problemMessages = $validationResult->getProblemMessages();

        $this->assertNotNull($problemMessages);
        $this->assertArrayHasKey('/foo', $problemMessages, 'problem was not set for /foo');
        $this->assertStringContainsString(
            'input1, input2',
            $problemMessages['/foo']
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
        $validator = new ParamsValidator();
        $validationResult = $rule->process('foo', $testValue, $validator);

        if ($expectError) {
            $this->assertNotNull($validationResult->getProblemMessages());
            return;
        }

        $this->assertEmpty($validationResult->getProblemMessages());
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }
}
