<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use ParamsTest\BaseTestCase;
use Params\ProcessRule\PositiveInt;
use Params\ParamsValuesImpl;

/**
 * @coversNothing
 */
class PositiveIntTest extends BaseTestCase
{
    public function provideTestCases()
    {
        return [
            ['5', 5, false],
            ['5.5', null, true], // not an int
            ['banana', null, true], // not an int
            ['0', 0, false], // close enough
            [PositiveInt::MAX_SANE_VALUE + 1 , null, true],
            [PositiveInt::MAX_SANE_VALUE, PositiveInt::MAX_SANE_VALUE, false],
            [PositiveInt::MAX_SANE_VALUE - 1, PositiveInt::MAX_SANE_VALUE - 1, false],
        ];
    }

    /**
     * @dataProvider provideTestCases
     * @covers \Params\ProcessRule\PositiveInt
     */
    public function testValidation($testValue, $expectedResult, $expectError)
    {
        $rule = new PositiveInt();
        $validator = new ParamsValuesImpl();
        $validationResult = $rule->process('foo', $testValue, $validator);
        if ($expectError == true) {
            $this->assertNotNull($validationResult->getValidationProblems());
        }
        else {
            $this->assertEmpty($validationResult->getValidationProblems());
            $this->assertEquals($validationResult->getValue(), $expectedResult);
        }
    }
}
