<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\DataLocator\StandardDataLocator;
use ParamsTest\BaseTestCase;
use Params\ProcessRule\MinLength;
use Params\ParamsValuesImpl;
use Params\Path;
use function Params\createPath;

/**
 * @coversNothing
 */
class MinLengthTest extends BaseTestCase
{
    public function provideMaxLengthCases()
    {
        $minLength = 8;
        $underLengthMinString = str_repeat('a', $minLength - 1);
        $exactLengthMinString = str_repeat('a', $minLength);
        $overLengthMinString = str_repeat('a', $minLength + 1);

        // Test the edge behaviour around partially multibyte strings
        $underLengthMinWithMBString = str_repeat('a', $minLength - 2) . "\xC2\xA3";
        $exactLengthMinWithMBString = str_repeat('a', $minLength - 1) . "\xC2\xA3";
        $overLengthMinWithMBString = str_repeat('a', $minLength) . "\xC2\xA3";

        // Test the edge behaviour around strings that are only MB characters
        $underLengthMinMBStringOnly = str_repeat("\xC2\xA3", $minLength - 1);
        $exactLengthMinMBStringOnly = str_repeat("\xC2\xA3", $minLength);
        $overLengthMinMBStringOnly = str_repeat("\xC2\xA3", $minLength + 1);

        return [
            [$minLength, $underLengthMinString, true],
            [$minLength, $exactLengthMinString, false],
            [$minLength, $overLengthMinString, false],

            [$minLength, $underLengthMinWithMBString, true],
            [$minLength, $exactLengthMinWithMBString, false],
            [$minLength, $overLengthMinWithMBString, false],

            [$minLength, $underLengthMinMBStringOnly, true],
            [$minLength, $exactLengthMinMBStringOnly, false],
            [$minLength, $overLengthMinMBStringOnly, false],
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
        $dataLocator = StandardDataLocator::fromArray([]);
        $validationResult = $rule->process(
            Path::fromName('foo'),
            $string,
            $validator,
            $dataLocator
        );

        if ($expectError === false) {
            $this->assertNoValidationProblems($validationResult->getValidationProblems());
        }
        else {
            // TODO - test against strings
            $this->assertExpectedValidationProblems($validationResult->getValidationProblems());
        }
    }
}
