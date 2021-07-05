<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\InputStorage\ArrayInputStorage;
use Params\Messages;
use Params\OpenApi\OpenApiV300ParamDescription;
use ParamsTest\BaseTestCase;
use Params\ProcessRule\MinFloatValue;
use Params\ProcessedValues;

/**
 * @coversNothing
 */
class MinFloatValueTest extends BaseTestCase
{
    public function provideMinFloatValueCases()
    {
        $minValue = 100.5;
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
     * @dataProvider provideMinFloatValueCases
     * @covers \Params\ProcessRule\MinFloatValue
     */
    public function testValidation(float $minValue, string $inputValue)
    {
        $rule = new MinFloatValue($minValue);
        $processedValues = new ProcessedValues();
        $dataLocator = ArrayInputStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $inputValue, $processedValues, $dataLocator
        );

        $this->assertNoProblems($validationResult);
    }


    public function provideMinFloatValueErrors()
    {
        $minValue = 100.5;
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
     * @dataProvider provideMinFloatValueErrors
     * @covers \Params\ProcessRule\MinFloatValue
     */
    public function testErrors(float $minValue, string $inputValue)
    {
        $rule = new MinFloatValue($minValue);
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
     * @covers \Params\ProcessRule\MinFloatValue
     */
    public function testDescription()
    {
        $description = new OpenApiV300ParamDescription('John');
        $minValue = 20.0;
        $rule = new MinFloatValue($minValue);
        $rule->updateParamDescription($description);


        $this->assertSame($minValue, $description->getMinimum());
        $this->assertFalse($description->isExclusiveMinimum());
    }
}
