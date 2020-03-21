<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use Params\ProcessRule\FloatInput;
use ParamsTest\BaseTestCase;
use Params\ParamsValidator;

/**
 * @coversNothing
 */
class FloatInputTest extends BaseTestCase
{
    public function provideWorksCases()
    {
        return [
            ['5', 5],
            ['555555', 555555],
            ['1000.1', 1000.1],
            ['-1000.1', -1000.1],
        ];
    }

    /**
     * @dataProvider provideWorksCases
     * @covers \Params\ProcessRule\FloatInput
     */
    public function testValidationWorks(string $inputValue, float $expectedValue)
    {
        $rule = new FloatInput();
        $validator = new ParamsValidator();
        $validationResult = $rule->process('foo', $inputValue, $validator);

        $this->assertEmpty($validationResult->getProblemMessages());
        $this->assertEquals($expectedValue, $validationResult->getValue());
    }

    public function provideErrorCases()
    {
        return [
            // todo - we should test the exact error.
            ['5.a'],
            ['5.5'],
            ['banana'],
        ];
    }

    /**
     * @dataProvider provideErrorCases
     * @covers \Params\ProcessRule\FloatInput
     */
    public function testValidationErrors(string $inputValue)
    {
        $rule = new FloatInput();
        $validator = new ParamsValidator();
        $validationResult = $rule->process('foo', $inputValue, $validator);
        $this->assertNotNull($validationResult->getProblemMessages());
    }
}
