<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use VarMap\ArrayVarMap;
use ParamsTest\BaseTestCase;
use Params\ExtractRule\GetBool;
use Params\ParamsValidator;

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
        $rule = new GetBool('foo');
        $validator = new ParamsValidator();
        $validationResult = $rule->process('foo', new ArrayVarMap([]), $validator);
        $this->assertNotNull($validationResult->getProblemMessages());
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
        $validator = new ParamsValidator();
        $rule = new GetBool('foo');
        $validationResult = $rule->process(
            $variableName,
            new ArrayVarMap([$variableName => $input]),
            $validator
        );

        $this->assertEmpty($validationResult->getProblemMessages());
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

        $rule = new GetBool($variableName);
        $validator = new ParamsValidator();
        $validationResult = $rule->process(
            $variableName,
            new ArrayVarMap($variables),
            $validator
        );

        $this->assertNotNull($validationResult->getProblemMessages());
    }
}
