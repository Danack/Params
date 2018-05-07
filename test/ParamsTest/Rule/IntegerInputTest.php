<?php

declare(strict_types=1);

namespace ParamsTest\Api\Params\Validator;

use Params\Rule\IntegerInput;
use ParamsTest\BaseTestCase;

class IntegerInputValidatorTest extends BaseTestCase
{
    public function provideMinIntValueCases()
    {
        return [
            ['5', 5, false],
            ['-5', null, true],
            ['5.5', null, true],
            ['banana', null, true],
            ['555555', 555555, false],
            [str_repeat('5', 20), null, true],
        ];
    }

    /**
     * @dataProvider provideMinIntValueCases
     * @covers \Params\Rule\IntegerInput
     */
    public function testValidation(string $inputValue, ?int $expectedValue, bool $expectError)
    {
        $validator = new IntegerInput();
        $validationResult = $validator('foo', $inputValue);

        if ($expectError === false) {
            $this->assertNull($validationResult->getProblemMessage());
            $this->assertEquals($expectedValue, $validationResult->getValue());
        }
        else {
            $this->assertNotNull($validationResult->getProblemMessage());
        }
    }
}
