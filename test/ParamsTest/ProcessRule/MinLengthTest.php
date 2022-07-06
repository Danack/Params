<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Type\DataStorage\TestArrayDataStorage;
use Type\Messages;
use Type\OpenApi\OpenApiV300ParamDescription;
use ParamsTest\BaseTestCase;
use Type\ProcessRule\MinLength;
use Type\ProcessedValues;

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
//            [$minLength, $underLengthMinString, true],
            [$minLength, $exactLengthMinString, false],
            [$minLength, $overLengthMinString, false],

//            [$minLength, $underLengthMinWithMBString, true],
            [$minLength, $exactLengthMinWithMBString, false],
            [$minLength, $overLengthMinWithMBString, false],

//            [$minLength, $underLengthMinMBStringOnly, true],
            [$minLength, $exactLengthMinMBStringOnly, false],
            [$minLength, $overLengthMinMBStringOnly, false],
        ];
    }

    /**
     * @dataProvider provideMaxLengthCases
     * @covers \Type\ProcessRule\MinLength
     */
    public function testValidation(int $minLength, string $string)
    {
        $rule = new MinLength($minLength);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $string, $processedValues, $dataStorage
        );

        $this->assertNoProblems($validationResult);
    }


    public function provideMinLengthErrors()
    {
        $minLength = 8;
        $underLengthMinString = str_repeat('a', $minLength - 1);

        // Test the edge behaviour around partially multibyte strings
        $underLengthMinWithMBString = str_repeat('a', $minLength - 2) . "\xC2\xA3";

        // Test the edge behaviour around strings that are only MB characters
        $underLengthMinMBStringOnly = str_repeat("\xC2\xA3", $minLength - 1);

        return [
            [$minLength, $underLengthMinString, true],
            [$minLength, $underLengthMinWithMBString, true],
            [$minLength, $underLengthMinMBStringOnly, true],
        ];
    }

    /**
     * @dataProvider provideMinLengthErrors
     * @covers \Type\ProcessRule\MinLength
     */
    public function testErrors(int $minLength, string $string)
    {
        $rule = new MinLength($minLength);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValue('foo', $string);
        $validationResult = $rule->process(
            $string, $processedValues, $dataStorage
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::STRING_TOO_SHORT,
            $validationResult->getValidationProblems()
        );

        $this->assertOneErrorAndContainsString($validationResult, (string)$minLength);
    }


    /**
     * @covers \Type\ProcessRule\MinLength
     */
    public function testDescription()
    {
        $minLength = 20;
        $rule = new MinLength($minLength);
        $description = $this->applyRuleToDescription($rule);
        $this->assertSame($minLength, $description->getMinLength());
    }
}
