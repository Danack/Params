<?php

declare(strict_types=1);

namespace ParamsTest\ExtractRule;

use Params\DataLocator\DataStorage;
use ParamsTest\BaseTestCase;
use Params\ExtractRule\GetFloat;
use Params\ProcessedValues;

/**
 * @coversNothing
 */
class GetFloatTest extends BaseTestCase
{
    /**
     * @covers \Params\ExtractRule\GetFloat
     */
    public function testMissingGivesError()
    {
        $rule = new GetFloat();
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator, DataStorage::fromArraySetFirstValue([])
        );
        $this->assertExpectedValidationProblems($validationResult->getValidationProblems());
    }

    public function provideTestWorksCases()
    {
        return [
            ['5', 5],
            ['555555', 555555],
            ['1000.1', 1000.1],
            ['-1000.1', -1000.1],
        ];
    }

    /**
     * @covers \Params\ExtractRule\GetFloat
     * @dataProvider provideTestWorksCases
     */
    public function testWorks($input, $expectedValue)
    {
        $variableName = 'foo';
        $validator = new ProcessedValues();
        $rule = new GetFloat();
        $validationResult = $rule->process(
            $validator, DataStorage::fromArraySetFirstValue([$variableName => $input])
        );

        $this->assertNoValidationProblems($validationResult->getValidationProblems());
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }

    public function provideTestErrorCases()
    {
        return [
            // todo - we should test the exact error.
            [['5.a']],
//            [['5.5']],
            [['banana']],
        ];
    }

    /**
     * @covers \Params\ExtractRule\GetFloat
     * @dataProvider provideTestErrorCases
     */
    public function testErrors($variables)
    {
        $rule = new GetFloat();
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator, DataStorage::fromArraySetFirstValue($variables)
        );

        $this->assertExpectedValidationProblems($validationResult->getValidationProblems());
    }
}
