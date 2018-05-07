<?php

declare(strict_types=1);

namespace ParamsTest\Api\Params\Validator;

use VarMap\ArrayVarMap;
use ParamsTest\BaseTestCase;
use Params\Rule\CheckSetOrDefault;

class CheckSetOrDefaultTest extends BaseTestCase
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
     * @covers \Params\Rule\CheckSetOrDefault
     */
    public function testValidation(ArrayVarMap $varMap, $default, $expectedValue)
    {
        $validator = new CheckSetOrDefault($default, $varMap);
        $validationResult = $validator('foo', null);

        $this->assertNull($validationResult->getProblemMessage());
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }
}
