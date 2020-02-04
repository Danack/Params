<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use Params\SubsequentRule\BoolInput;
use ParamsTest\BaseTestCase;
use Params\ParamsValidator;

/**
 * @coversNothing
 */
class BoolInputValidatorTest extends BaseTestCase
{
    public function provideBoolValueWorksCases()
    {
        return [
            ['true', true],
            ['truuue', false],

            [0, false],
            [1, true],
            [2, true],
            [-5000, true],
        ];
    }

    /**
     * @dataProvider provideBoolValueWorksCases
     * @covers \Params\SubsequentRule\IntegerInput
     */
    public function testValidationWorks($inputValue, bool $expectedValue)
    {
        $rule = new BoolInput();
        $validator = new ParamsValidator();
        $validationResult = $rule->process('foo', $inputValue, $validator);

        $this->assertEmpty($validationResult->getProblemMessages());
        $this->assertEquals($expectedValue, $validationResult->getValue());
    }

    public function provideBoolValueErrorsCases()
    {
        return [
            // todo - we should test the exact error.
            [fopen('php://memory', 'r+')],
            [[1, 2, 3]],
            [new \StdClass()]
        ];
    }

    /**
     * @dataProvider provideBoolValueErrorsCases
     * @covers \Params\SubsequentRule\IntegerInput
     */
    public function testValidationErrors($inputValue)
    {
        $rule = new BoolInput();
        $validator = new ParamsValidator();
        $validationResult = $rule->process('foo', $inputValue, $validator);
        $this->assertNotNull($validationResult->getProblemMessages());
    }
}
