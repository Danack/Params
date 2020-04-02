<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use ParamsTest\BaseTestCase;
use Params\ProcessRule\MinLength;
use Params\ParamsValuesImpl;
use Params\Path;

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
     * @covers \Params\ProcessRule\MinLength
     */
    public function testValidation(int $minLength, string $string, bool $expectError)
    {
        $rule = new MinLength($minLength);
        $validator = new ParamsValuesImpl();
        $validationResult = $rule->process(
            Path::fromName('foo'),
            $string,
            $validator
        );

        if ($expectError === false) {
            $this->assertEmpty($validationResult->getValidationProblems());
        }
        else {
            $this->assertNotNull($validationResult->getValidationProblems());
        }
    }
}
