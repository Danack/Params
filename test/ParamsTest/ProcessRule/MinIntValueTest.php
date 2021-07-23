<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\InputStorage\ArrayInputStorage;
use Params\Messages;
use Params\OpenApi\OpenApiV300ParamDescription;
use ParamsTest\BaseTestCase;
use Params\ProcessRule\MinIntValue;
use Params\ProcessedValues;

/**
 * @coversNothing
 */
class MinIntValueTest extends BaseTestCase
{
    public function provideMinIntValueCases()
    {
        $minValue = 100;
        $underValue = $minValue - 1;
        $exactValue = $minValue ;
        $overValue = $minValue + 1;

        return [
//            [$minValue, (string)$underValue, true],
            [$minValue, (string)$exactValue],
            [$minValue, (string)$overValue],

            // TODO - think about these cases.
//            [$minValue, 'banana', true]
        ];
    }

    /**
     * @dataProvider provideMinIntValueCases
     * @covers \Params\ProcessRule\MinIntValue
     */
    public function testValidation(int $minValue, string $inputValue)
    {
        $rule = new MinIntValue($minValue);
        $processedValues = new ProcessedValues();
        $dataLocator = ArrayInputStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $inputValue, $processedValues, $dataLocator
        );

        $this->assertNoProblems($validationResult);
    }


    public function provideMinIntValueErrors()
    {
        $minValue = 100;
        $underValue = $minValue - 1;
        $exactValue = $minValue ;
        $overValue = $minValue + 1;

        return [
            [$minValue, (string)$underValue],

            // TODO - think about these cases.
            [$minValue, 'banana', true]
        ];
    }

    /**
     * @dataProvider provideMinIntValueErrors
     * @covers \Params\ProcessRule\MinIntValue
     */
    public function testErrors(int $minValue, string $inputValue)
    {
        $rule = new MinIntValue($minValue);
        $processedValues = new ProcessedValues();
        $dataLocator = ArrayInputStorage::fromSingleValue('foo', $inputValue);
        $validationResult = $rule->process(
            $inputValue, $processedValues, $dataLocator
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::INT_TOO_SMALL,
            $validationResult->getValidationProblems()
        );

        $this->assertOneErrorAndContainsString($validationResult, (string)$minValue);
    }

    /**
     * @covers \Params\ProcessRule\MinIntValue
     */
    public function testDescription()
    {
        $minValue = 20;
        $rule = new MinIntValue($minValue);
        $description = $this->applyRuleToDescription($rule);

        $this->assertSame($minValue, $description->getMinimum());
        $this->assertFalse($description->isExclusiveMinimum());
    }
}
