<?php

declare(strict_types=1);

namespace ParamsTest\ExtractRule;

use Params\DataLocator\StandardDataLocator;
use VarMap\ArrayVarMap;
use ParamsTest\BaseTestCase;
use Params\ExtractRule\GetBool;
use Params\ParamsValuesImpl;
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
        $validator = new ParamsValuesImpl();
        $validationResult = $rule->process(
            Path::fromName('foo'),
            new ArrayVarMap([]),
            $validator,
            StandardDataLocator::fromArray([])
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
        $variableName = 'foo';
        $validator = new ParamsValuesImpl();
        $rule = new GetBool();
        $validationResult = $rule->process(
            Path::fromName($variableName),
            new ArrayVarMap([$variableName => $input]),
            $validator,
            StandardDataLocator::fromArray(['foo' => $input])
        );

        $this->assertEmpty($validationResult->getValidationProblems());
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }

    public function provideTestErrorCases()
    {
        return [
            // todo - we should test the exact error.
            [['foo' => fopen('php://memory', 'r+')]],
            [['foo' => [1, 2, 3]]],
            [['foo' => new \StdClass()]]
        ];
    }

    /**
     * @covers \Params\ExtractRule\GetBool
     * @dataProvider provideTestErrorCases
     */
    public function testErrors($variables)
    {
        $variableName = 'foo';

        $rule = new GetBool();
        $validator = new ParamsValuesImpl();
        $validationResult = $rule->process(
            Path::fromName($variableName),
            new ArrayVarMap($variables),
            $validator,
            StandardDataLocator::fromArray(['foo' => $variables])
        );

        $this->assertExpectedValidationProblems($validationResult->getValidationProblems());
    }
}
