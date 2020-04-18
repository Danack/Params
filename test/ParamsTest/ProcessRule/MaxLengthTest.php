<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\DataLocator\DataStorage;
use ParamsTest\BaseTestCase;
use Params\ProcessRule\MaxLength;
use Params\ProcessedValuesImpl;

/**
 * @coversNothing
 */
class MaxLengthTest extends BaseTestCase
{
    public function provideMaxLengthCases()
    {
        $maxLength = 10;
        $underLengthString = str_repeat('a', $maxLength - 1);
        $exactLengthString = str_repeat('a', $maxLength);
        $overLengthString = str_repeat('a', $maxLength + 1);

        // Test the edge behaviour around multibyte strings
        $underLengthWithMBString = str_repeat('a', $maxLength - 2) . "\xC2\xA3";
        $exactLengthWithMBString = str_repeat('a', $maxLength - 1) . "\xC2\xA3";
        $overLengthWithMBString = str_repeat('a', $maxLength) . "\xC2\xA3";


        // Test the edge behaviour around strings that are only MB characters
        $underLengthMBStringOnly = str_repeat("\xC2\xA3", $maxLength - 1);
        $exactLengthMBStringOnly = str_repeat("\xC2\xA3", $maxLength);
        $overLengthMBStringOnly = str_repeat("\xC2\xA3", $maxLength + 1);

        return [
            [$maxLength, $underLengthString, false],
            [$maxLength, $exactLengthString, false],
            [$maxLength, $overLengthString, true],

            [$maxLength, $underLengthWithMBString, false],
            [$maxLength, $exactLengthWithMBString, false],
            [$maxLength, $overLengthWithMBString, true],

            [$maxLength, $underLengthMBStringOnly, false],
            [$maxLength, $exactLengthMBStringOnly, false],
            [$maxLength, $overLengthMBStringOnly, true],
        ];
    }

    /**
     * @dataProvider provideMaxLengthCases
     * @covers \Params\ProcessRule\MaxLength
     */
    public function testValidation(int $maxLength, string $string, bool $expectError)
    {
        $rule = new MaxLength($maxLength);
        $processedValues = new ProcessedValuesImpl();
        $dataLocator = DataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $string,
            $processedValues,
            $dataLocator
        );

        if ($expectError === false) {
            $this->assertNoValidationProblems($validationResult->getValidationProblems());
        }
        else {
            // TODO - replace this with text comparison.
            $this->assertExpectedValidationProblems($validationResult->getValidationProblems());
        }
    }
}
