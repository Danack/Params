<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\InputStorage\ArrayInputStorage;
use Params\Messages;
use Params\OpenApi\OpenApiV300ParamDescription;
use ParamsTest\BaseTestCase;
use Params\ProcessRule\MaxFloatValue;
use Params\ProcessedValues;

/**
 * @coversNothing
 */
class MaxFloatValueTest extends BaseTestCase
{
    public function provideMaxFloatCases()
    {
        $maxValue = 256.5;
        $underValue = $maxValue - 1;
        $exactValue = $maxValue ;
        $overValue = $maxValue + 1;

        return [
            [$maxValue, (string)$underValue],
            [$maxValue, (string)$exactValue],
            // TODO - think about these cases.
//            [$maxValue, 125.5, true],
//            [$maxValue, 'banana', true]
        ];
    }

    /**
     * @dataProvider provideMaxFloatCases
     * @covers \Params\ProcessRule\MaxFloatValue
     */
    public function testValidation(float $maxValue, string $inputValue)
    {
        $rule = new MaxFloatValue($maxValue);
        $processedValues = new ProcessedValues();
        $dataLocator = ArrayInputStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $inputValue,
            $processedValues,
            $dataLocator
        );


        $this->assertNoProblems($validationResult);
    }



    public function provideMaxFloatErrors()
    {
        $maxValue = 256;
        $underValue = $maxValue - 1;
        $exactValue = $maxValue ;
        $overValue = $maxValue + 1;

        return [
            [$maxValue, (string)$overValue],

            // TODO - think about these cases.
//            [$maxValue, 125.5, true],
//            [$maxValue, 'banana', true]
        ];
    }

    /**
     * @dataProvider provideMaxFloatErrors
     * @covers \Params\ProcessRule\MaxFloatValue
     */
    public function testErrors(float $maxValue, string $inputValue)
    {
        $rule = new MaxFloatValue($maxValue);
        $processedValues = new ProcessedValues();
        $dataLocator = ArrayInputStorage::fromSingleValue('foo', $inputValue);
        $validationResult = $rule->process(
            $inputValue,
            $processedValues,
            $dataLocator
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::INT_TOO_LARGE,
            $validationResult->getValidationProblems()
        );

        $this->assertOneErrorAndContainsString($validationResult, (string)$maxValue);
    }

    /**
     * @covers \Params\ProcessRule\MaxFloatValue
     */
    public function testDescription()
    {

        $maxValue = 20.0;
        $rule = new MaxFloatValue($maxValue);
        $description = $this->applyRuleToDescription($rule);

        $this->assertSame($maxValue, $description->getMaximum());
        $this->assertFalse($description->isExclusiveMaximum());
    }
}
