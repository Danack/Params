<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use ParamsTest\BaseTestCase;
use Params\SubsequentRule\EnumMap;
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
            'key1' => 'value1',
            'key2' => 'value2',
        ];

        $rule = new EnumMap($enumMap);
        $validator = new ParamsValidator();
        $validationResult = $rule->process('foo', 'unknown value', $validator);


        $problemMessages = $validationResult->getProblemMessages();

        $this->assertNotNull($problemMessages);

        $this->assertStringContainsString(
            'key1, key2',
            $problemMessages[0]
        );
    }

    /**
     * @dataProvider provideTestCases
     * @covers \Params\SubsequentRule\EnumMap
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
