<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use VarMap\ArrayVarMap;
use ParamsTest\BaseTestCase;
use Params\ExtractRule\GetInt;
use Params\ParamsValuesImpl;
use Params\Path;

/**
 * @coversNothing
 */
class GetIntTest extends BaseTestCase
{
    /**
     * @covers \Params\ExtractRule\GetString
     */
    public function testMissingGivesError()
    {
        $rule = new GetInt();
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
            ['5', 5],
            [5, 5],
        ];
    }

    /**
     * @covers \Params\ExtractRule\GetInt
     * @dataProvider provideTestWorksCases
     */
    public function testWorks($input, $expectedValue)
    {
        $variableName = 'foo';
        $validator = new ParamsValuesImpl();
        $rule = new GetInt();
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
            [['foo', null]],
            [['foo', '']],
            [['foo', '6 apples']],
            [['foo', 'banana']],
        ];
    }

    /**
     * @covers \Params\ExtractRule\GetInt
     * @dataProvider provideTestErrorCases
     */
    public function testErrors($variables)
    {
        $variableName = 'foo';

        $rule = new GetInt();
        $validator = new ParamsValuesImpl();
        $validationResult = $rule->process(
            Path::fromName($variableName),
            new ArrayVarMap($variables),
            $validator
        );

        $this->assertNotNull($validationResult->getValidationProblems());
    }
}
