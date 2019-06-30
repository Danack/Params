<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use ParamsTest\BaseTestCase;
use Params\SubsequentRule\MinLength;
use Params\ParamsValidator;

/**
 * @coversNothing
 */
class MinLengthTest extends BaseTestCase
{
    public function provideMaxLengthCases()
    {
        $length = 8;
        $underLengthString = str_repeat('a', $length - 1);
        $exactLengthString = str_repeat('a', $length);
        $overLengthString = str_repeat('a', $length + 1);

        return [
            [$length, $underLengthString, true],
            [$length, $exactLengthString, false],
            [$length, $overLengthString, false],
        ];
    }

    /**
     * @dataProvider provideMaxLengthCases
     * @covers \Params\SubsequentRule\MinLength
     */
    public function testValidation(int $minLength, string $string, bool $expectError)
    {
        $rule = new MinLength($minLength);
        $validator = new ParamsValidator();
        $validationResult = $rule->process('foo', $string, $validator);

        if ($expectError === false) {
            $this->assertEmpty($validationResult->getProblemMessages());
        }
        else {
            $this->assertNotNull($validationResult->getProblemMessages());
        }
    }
}
