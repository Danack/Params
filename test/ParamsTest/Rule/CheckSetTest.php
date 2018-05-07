<?php

declare(strict_types=1);

namespace ParamsTest\Api\Params\Validator;

use VarMap\ArrayVarMap;
use ParamsTest\BaseTestCase;
use Params\Rule\CheckSet;

class CheckSetTest extends BaseTestCase
{
    /**
     * @covers \Params\Rule\CheckSet
     */
    public function testMissingGivesError()
    {
        $validator = new CheckSet(new ArrayVarMap([]));
        $validationResult = $validator('foo', 'not_used');
        $this->assertNotNull($validationResult->getProblemMessage());
    }

    /**
     * @covers \Params\Rule\CheckSet
     */
    public function testValidation()
    {
        $variableName = 'foo';
        $expectedValue = 'bar';

        $validator = new CheckSet(new ArrayVarMap([$variableName => $expectedValue]));
        $validationResult = $validator($variableName, 'not_used');

        $this->assertNull($validationResult->getProblemMessage());
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }
}
