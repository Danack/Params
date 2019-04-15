<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use ParamsTest\BaseTestCase;
use Params\Rule\MaxIntValue;

/**
 * @coversNothing
 */
class MaxIntValueValidatorTest extends BaseTestCase
{
    public function provideMaxLengthCases()
    {
        $maxValue = 256;
        $underValue = $maxValue - 1;
        $exactValue = $maxValue ;
        $overValue = $maxValue + 1;

        return [
            [$maxValue, (string)$underValue, false],
            [$maxValue, (string)$exactValue, false],
            [$maxValue, (string)$overValue, true],

            // TODO - think about these cases.
//            [$maxValue, 125.5, true],
//            [$maxValue, 'banana', true]
        ];
    }

    /**
     * @dataProvider provideMaxLengthCases
     * @covers \Params\Rule\MaxIntValue
     */
    public function testValidation(int $maxValue, string $inputValue, bool $expectError)
    {
        $validator = new MaxIntValue($maxValue);
        $validationResult = $validator('foo', $inputValue);

        if ($expectError === false) {
            $this->assertNull($validationResult->getProblemMessage());
        }
        else {
            $this->assertNotNull($validationResult->getProblemMessage());
        }
    }
}
