<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\InputStorage\ArrayInputStorage;
use Params\Messages;
use Params\OpenApi\OpenApiV300ParamDescription;
use ParamsTest\BaseTestCase;
use Params\ProcessRule\MaxLength;
use Params\ProcessedValues;

/**
 * @coversNothing
 */
class MaxLengthTest extends BaseTestCase
{
    public function provideMaxLengthWorks()
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
            [$maxLength, $underLengthString],
            [$maxLength, $exactLengthString],

            [$maxLength, $underLengthWithMBString],
            [$maxLength, $exactLengthWithMBString],

            [$maxLength, $underLengthMBStringOnly],
            [$maxLength, $exactLengthMBStringOnly],
        ];
    }

    /**
     * @dataProvider provideMaxLengthWorks
     * @covers \Params\ProcessRule\MaxLength
     */
    public function testValidationWorks(int $maxLength, string $string)
    {
        $rule = new MaxLength($maxLength);
        $processedValues = new ProcessedValues();
        $dataLocator = ArrayInputStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $string,
            $processedValues,
            $dataLocator
        );

        $this->assertNoProblems($validationResult);
    }

    public function provideMaxLengthErrors()
    {
        $maxLength = 10;
        $overLengthString = str_repeat('a', $maxLength + 1);

        // Test the edge behaviour around multibyte strings
        $overLengthWithMBString = str_repeat('a', $maxLength) . "\xC2\xA3";

        // Test the edge behaviour around strings that are only MB characters
        $overLengthMBStringOnly = str_repeat("\xC2\xA3", $maxLength + 1);

        return [
            [$maxLength, $overLengthString],
            [$maxLength, $overLengthWithMBString],
            [$maxLength, $overLengthMBStringOnly],
        ];
    }

    /**
     * @dataProvider provideMaxLengthErrors
     * @covers \Params\ProcessRule\MaxLength
     */
    public function testErrors(int $maxLength, string $string)
    {
        $rule = new MaxLength($maxLength);
        $processedValues = new ProcessedValues();
        $dataLocator = ArrayInputStorage::fromSingleValue('foo', $string);
        $validationResult = $rule->process(
            $string,
            $processedValues,
            $dataLocator
        );

        // TODO - replace this with text comparison.
        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::STRING_TOO_LONG,
            $validationResult->getValidationProblems()
        );
    }


    /**
     * @covers \Params\ProcessRule\MaxLength
     */
    public function testDescription()
    {
        $maxLength = 20;
        $rule = new MaxLength($maxLength);
        $description = $this->applyRuleToDescription($rule);
        $this->assertSame($maxLength, $description->getMaxLength());
    }
}
