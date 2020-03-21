<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use Params\ProcessRule\IntegerInput;
use ParamsTest\BaseTestCase;
use Params\ParamsValidator;

/**
 * @coversNothing
 * @group wip
 */
class IntegerInputTest extends BaseTestCase
{
    public function provideIntValueWorksCases()
    {
        return [
            ['5', 5],
            ['-10', -10],
            ['555555', 555555],
            [(string)IntegerInput::MAX_SANE_VALUE, IntegerInput::MAX_SANE_VALUE]
        ];
    }

    /**
     * @dataProvider provideIntValueWorksCases
     * @covers \Params\ProcessRule\IntegerInput
     */
    public function testValidationWorks(string $inputValue, int $expectedValue)
    {
        $rule = new IntegerInput();
        $validator = new ParamsValidator();
        $validationResult = $rule->process('foo', $inputValue, $validator);

        $this->assertEmpty($validationResult->getProblemMessages());
        $this->assertEquals($expectedValue, $validationResult->getValue());
    }

    public function provideMinIntValueErrorsCases()
    {
        return [
            // todo - we should test the exact error.
            ['-5'],
            ['5.5'],
            ['banana'],
            [''],
            [(string)(IntegerInput::MAX_SANE_VALUE + 1)]
        ];
    }

    /**
     * @dataProvider provideMinIntValueErrorsCases
     * @covers \Params\ProcessRule\IntegerInput
     */
    public function testValidationErrors(string $inputValue)
    {
        $rule = new IntegerInput();
        $validator = new ParamsValidator();
        $validationResult = $rule->process('foo', $inputValue, $validator);
        $this->assertNotNull($validationResult->getProblemMessages());
    }
}
