<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use ParamsTest\BaseTestCase;
use Params\ProcessRule\Enum;
use Params\ParamsValuesImpl;
use Params\Path;

/**
 * @coversNothing
 */
class EnumTest extends BaseTestCase
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
     * @covers \Params\ProcessRule\Enum
     */
    public function testValidation($testValue, $expectError, $expectedValue)
    {
        $enumValues = ['zoq', 'fot', 'pik', '12345'];

        $rule = new Enum($enumValues);
        $validator = new ParamsValuesImpl();
        $validationResult = $rule->process(
            Path::fromName('foo'),
            $testValue,
            $validator
        );

        if ($expectError) {
            $this->assertNotNull($validationResult->getValidationProblems());
            return;
        }

        $this->assertEmpty($validationResult->getValidationProblems());
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }
}
