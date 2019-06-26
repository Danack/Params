<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use VarMap\ArrayVarMap;
use ParamsTest\BaseTestCase;
use Params\FirstRule\GetOptionalString;
use Params\ParamsValidator;

/**
 * @coversNothing
 */
class GetOptionalStringTest extends BaseTestCase
{
    /**
     * @covers \Params\FirstRule\GetOptionalString
     */
    public function testMissingGivesNull()
    {
        $rule = new GetOptionalString();
        $validator = new ParamsValidator();

        $validationResult = $rule->process('foo', new ArrayVarMap([]), $validator);
        $this->assertNull($validationResult->getProblemMessage());
        $this->assertNull($validationResult->getValue());
    }

    /**
     * @covers \Params\FirstRule\GetOptionalString
     */
    public function testValidation()
    {
        $variableName = 'foo';
        $expectedValue = 'bar';

        $varMap = new ArrayVarMap([$variableName => $expectedValue]);
        $rule = new GetOptionalString();
        $validator = new ParamsValidator();
        $validationResult = $rule->process($variableName, $varMap, $validator);

        $this->assertNull($validationResult->getProblemMessage());
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }
}
