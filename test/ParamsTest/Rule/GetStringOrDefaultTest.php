<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use VarMap\ArrayVarMap;
use ParamsTest\BaseTestCase;
use Params\FirstRule\GetStringOrDefault;
use Params\ParamsValidator;

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
     * @covers \Params\FirstRule\GetStringOrDefault
     */
    public function testValidation(ArrayVarMap $varMap, $default, $expectedValue)
    {
        $rule = new GetStringOrDefault($default);
        $validator = new ParamsValidator();
        $validationResult = $rule->process('foo', $varMap, $validator);

        $this->assertNull($validationResult->getProblemMessage());
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }
}
