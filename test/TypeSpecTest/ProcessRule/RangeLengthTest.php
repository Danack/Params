<?php

declare(strict_types=1);

namespace TypeSpecTest\ProcessRule;

use TypeSpec\DataStorage\TestArrayDataStorage;
use TypeSpec\Messages;
use TypeSpecTest\BaseTestCase;
use TypeSpec\ProcessRule\RangeStringLength;
use TypeSpec\ProcessedValues;

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
            [$minLength, $maxLength, $underLengthMinString, Messages::STRING_TOO_SHORT],
            [$minLength, $maxLength, $exactLengthMinString, null],
            [$minLength, $maxLength, $overLengthMinString, null],

            [$minLength, $maxLength, $underLengthMinWithMBString, Messages::STRING_TOO_SHORT],
            [$minLength, $maxLength, $exactLengthMinWithMBString, null],
            [$minLength, $maxLength, $overLengthMinWithMBString, null],

            [$minLength, $maxLength, $underLengthMinMBStringOnly, Messages::STRING_TOO_SHORT],
            [$minLength, $maxLength, $exactLengthMinMBStringOnly, null],
            [$minLength, $maxLength, $overLengthMinMBStringOnly, null],

            [$minLength, $maxLength, $underLengthMaxString, null],
            [$minLength, $maxLength, $exactLengthMaxString, null],
            [$minLength, $maxLength, $overLengthMaxString, Messages::STRING_TOO_LONG],

            [$minLength, $maxLength, $underLengthMaxWithMBString, null],
            [$minLength, $maxLength, $exactLengthMaxWithMBString, null],
            [$minLength, $maxLength, $overLengthMaxWithMBString, Messages::STRING_TOO_LONG],

            [$minLength, $maxLength, $underLengthMaxMBStringOnly, null],
            [$minLength, $maxLength, $exactLengthMaxMBStringOnly, null],
            [$minLength, $maxLength, $overLengthMaxMBStringOnly, Messages::STRING_TOO_LONG],
        ];
    }

    /**
     * @dataProvider provideMaxLengthCases
     * @covers \TypeSpec\ProcessRule\RangeStringLength
     */
    public function testValidation(
        int $minLength,
        int $maxLength,
        string $string,
        ?string $expectedError
    ) {
        $rule = new RangeStringLength($minLength, $maxLength);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $string);
        $validationResult = $rule->process(
            $string, $processedValues, $dataStorage
        );

        if ($expectedError === null) {
            $this->assertNoProblems($validationResult);
        }
        else {
            $this->assertValidationProblemRegexp(
                '/foo',
                $expectedError,
                $validationResult->getValidationProblems()
            );
        }
    }


    /**
     * @covers \TypeSpec\ProcessRule\RangeStringLength
     */
    public function testDescription()
    {
        $rule = new RangeStringLength(10, 20);
        $description = $this->applyRuleToDescription($rule);

        $this->assertSame(10, $description->getMinLength());
        $this->assertSame(20, $description->getMaxLength());
    }
}
