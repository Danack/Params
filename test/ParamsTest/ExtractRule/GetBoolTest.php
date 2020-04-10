<?php

declare(strict_types=1);

namespace ParamsTest\ExtractRule;

use Params\DataLocator\DataStorage;
use VarMap\ArrayVarMap;
use ParamsTest\BaseTestCase;
use Params\ExtractRule\GetBool;
use Params\ProcessedValuesImpl;
use Params\Path;

/**
 * @coversNothing
 */
class GetBoolTest extends BaseTestCase
{
    /**
     * @covers \Params\ExtractRule\GetString
     */
    public function testMissingGivesError()
    {
        $rule = new GetBool();
        $validator = new ProcessedValuesImpl();
        $validationResult = $rule->process(
            $validator, DataStorage::fromArraySetFirstValue([])
        );
        $this->assertExpectedValidationProblems($validationResult->getValidationProblems());
    }

    public function provideTestWorksCases()
    {
        yield ['true', true];
        yield ['truuue', false];
        yield [null, false];

        yield [0, false];
        yield [1, true];
        yield [2, true];
        yield [-5000, true];
    }

    /**
     * @covers \Params\ExtractRule\GetBool
     * @dataProvider provideTestWorksCases
     */
    public function testWorks($input, $expectedValue)
    {

        $validator = new ProcessedValuesImpl();
        $rule = new GetBool();
        $validationResult = $rule->process(
            $validator,
            DataStorage::fromSingleValue('foo', $input)
        );

        $this->assertNoValidationProblems($validationResult->getValidationProblems());
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }

    public function provideTestErrorCases()
    {
        return [
            // todo - we should test the exact error.
            [fopen('php://memory', 'r+')],
            [[1, 2, 3]],
            [new \StdClass()]
        ];
    }

    /**
     * @covers \Params\ExtractRule\GetBool
     * @dataProvider provideTestErrorCases
     */
    public function testErrors($value)
    {
        $rule = new GetBool();
        $validator = new ProcessedValuesImpl();
        $validationResult = $rule->process(
            $validator,
            DataStorage::fromArraySetFirstValue(['foo' => $value])
        );

        $this->assertExpectedValidationProblems($validationResult->getValidationProblems());
    }
}
