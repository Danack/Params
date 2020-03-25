<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use VarMap\ArrayVarMap;
use ParamsTest\BaseTestCase;
use Params\ExtractRule\GetStringOrDefault;
use Params\ParamsValuesImpl;
use Params\Path;

/**
 * @coversNothing
 */
class GetStringOrDefaultTest extends BaseTestCase
{
    public function provideTestCases()
    {
        return [
            [new ArrayVarMap(['foo' => 'bar']), 'john', 'bar'],
            [new ArrayVarMap([]), 'john', 'john'],
            [new ArrayVarMap([]), null, null],
        ];
    }

    /**
     * @dataProvider provideTestCases
     * @covers \Params\ExtractRule\GetStringOrDefault
     */
    public function testValidation(ArrayVarMap $varMap, $default, $expectedValue)
    {
        $rule = new GetStringOrDefault($default);
        $validator = new ParamsValuesImpl();
        $validationResult = $rule->process(
            Path::fromName('foo'),
            $varMap,
            $validator
        );

        $this->assertEmpty($validationResult->getValidationProblems());
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }
}
