<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\DataLocator\DataStorage;
use ParamsTest\BaseTestCase;
use Params\ProcessRule\RangeLength;
use Params\ProcessedValues;

/**
 * @coversNothing
 */
class RangeLengthTest extends BaseTestCase
{
    public function provideMaxLengthCases()
    {
        $maxLength = 12;
        $underLengthMaxString = str_repeat('a', $maxLength - 1);
        $exactLengthMaxString = str_repeat('a', $maxLength);
        $overLengthMaxString = str_repeat('a', $maxLength + 1);

        // Test the edge behaviour around multibyte strings
        $underLengthMaxWithMBString = str_repeat('a', $maxLength - 2) . "\xC2\xA3";
        $exactLengthMaxWithMBString = str_repeat('a', $maxLength - 1) . "\xC2\xA3";
        $overLengthMaxWithMBString = str_repeat('a', $maxLength) . "\xC2\xA3";

        // Test the edge behaviour around strings that are only MB characters
        $underLengthMaxMBStringOnly = str_repeat("\xC2\xA3", $maxLength - 1);
        $exactLengthMaxMBStringOnly = str_repeat("\xC2\xA3", $maxLength);
        $overLengthMaxMBStringOnly = str_repeat("\xC2\xA3", $maxLength + 1);

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
            [$minLength, $maxLength, $underLengthMinString, true],
            [$minLength, $maxLength, $exactLengthMinString, false],
            [$minLength, $maxLength, $overLengthMinString, false],

            [$minLength, $maxLength, $underLengthMinWithMBString, true],
            [$minLength, $maxLength, $exactLengthMinWithMBString, false],
            [$minLength, $maxLength, $overLengthMinWithMBString, false],

            [$minLength, $maxLength, $underLengthMinMBStringOnly, true],
            [$minLength, $maxLength, $exactLengthMinMBStringOnly, false],
            [$minLength, $maxLength, $overLengthMinMBStringOnly, false],

            [$minLength, $maxLength, $underLengthMaxString, false],
            [$minLength, $maxLength, $exactLengthMaxString, false],
            [$minLength, $maxLength, $overLengthMaxString, true],

            [$minLength, $maxLength, $underLengthMaxWithMBString, false],
            [$minLength, $maxLength, $exactLengthMaxWithMBString, false],
            [$minLength, $maxLength, $overLengthMaxWithMBString, true],

            [$minLength, $maxLength, $underLengthMaxMBStringOnly, false],
            [$minLength, $maxLength, $exactLengthMaxMBStringOnly, false],
            [$minLength, $maxLength, $overLengthMaxMBStringOnly, true],
        ];
    }

    /**
     * @dataProvider provideMaxLengthCases
     * @covers \Params\ProcessRule\RangeLength
     */
    public function testValidation(int $minLength, int $maxLength, string $string, bool $expectError)
    {
        $rule = new RangeLength($minLength, $maxLength);
        $processedValues = new ProcessedValues();
        $dataLocator = DataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $string, $processedValues, $dataLocator
        );

        if ($expectError === false) {
            $this->assertNoProblems($validationResult);
        }
        else {
            // TODO - replace this with text comparison.
            $this->assertExpectedValidationProblems($validationResult->getValidationProblems());
        }
    }


    /**
     * @covers \Params\ProcessRule\RangeLength
     */
    public function testDescription()
    {
        $rule = new RangeLength(10, 20);
        $description = $this->applyRuleToDescription($rule);

        $this->assertSame(10, $description->getMinLength());
        $this->assertSame(20, $description->getMaxLength());
    }
}
