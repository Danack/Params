<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

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
            $validator
        );
        $this->assertNotNull($validationResult->getValidationProblems());
    }

    public function provideTestWorksCases()
    {
        return [
            ['true', true],
            ['truuue', false],
            [null, false],

            [0, false],
            [1, true],
            [2, true],
            [-5000, true],
        ];
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
            $validator
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
            $validator
        );

        $this->assertNotNull($validationResult->getValidationProblems());
    }
}
