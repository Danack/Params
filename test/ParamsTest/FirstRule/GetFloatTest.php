<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use VarMap\ArrayVarMap;
use ParamsTest\BaseTestCase;
use Params\ExtractRule\GetFloat;
use Params\ParamsValuesImpl;

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
        $validator = new ParamsValuesImpl();
        $validationResult = $rule->process('foo', new ArrayVarMap([]), $validator);
        $this->assertNotNull($validationResult->getValidationProblems());
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
        $validator = new ParamsValuesImpl();
        $rule = new GetFloat();
        $validationResult = $rule->process(
            $variableName,
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
            [['5.a']],
            [['5.5']],
            [['banana']],
        ];
    }

    /**
     * @covers \Params\ExtractRule\GetFloat
     * @dataProvider provideTestErrorCases
     */
    public function testErrors($variables)
    {
        $variableName = 'foo';

        $rule = new GetFloat();
        $validator = new ParamsValuesImpl();
        $validationResult = $rule->process(
            $variableName,
            new ArrayVarMap($variables),
            $validator
        );

        $this->assertNotNull($validationResult->getValidationProblems());
    }
}
