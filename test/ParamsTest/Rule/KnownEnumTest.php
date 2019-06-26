<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use ParamsTest\BaseTestCase;
use Params\SubsequentRule\Enum;
use Params\ParamsValidator;

/**
 * @coversNothing
 */
class KnownEnumValidatorTest extends BaseTestCase
{
    public function provideTestCases()
    {
        return [
            ['zoq', false, 'zoq'],
            ['Zebranky ', true, null],
            ['12345', false, '12345'],
            [12345, true, null]
        ];
    }

    /**
     * @dataProvider provideTestCases
     * @covers \Params\SubsequentRule\Enum
     */
    public function testValidation($testValue, $expectError, $expectedValue)
    {
        $knowValues = ['zoq', 'fot', 'pik', '12345'];

        $rule = new Enum($knowValues);
        $validator = new ParamsValidator();
        $validationResult = $rule->process('foo', $testValue, $validator);

        if ($expectError) {
            $this->assertNotNull($validationResult->getProblemMessage());
            return;
        }

        $this->assertNull($validationResult->getProblemMessage());
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }
}
