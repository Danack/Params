<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use VarMap\ArrayVarMap;
use ParamsTest\BaseTestCase;
use Params\ExtractRule\GetOptionalString;
use Params\ParamsValuesImpl;

/**
 * @coversNothing
 */
class GetOptionalStringTest extends BaseTestCase
{
    /**
     * @covers \Params\ExtractRule\GetOptionalString
     */
    public function testMissingGivesNull()
    {
        $rule = new GetOptionalString();
        $validator = new ParamsValuesImpl();

        $validationResult = $rule->process('foo', new ArrayVarMap([]), $validator);
        $this->assertEmpty($validationResult->getValidationProblems());
        $this->assertNull($validationResult->getValue());
    }

    /**
     * @covers \Params\ExtractRule\GetOptionalString
     */
    public function testValidation()
    {
        $variableName = 'foo';
        $expectedValue = 'bar';

        $varMap = new ArrayVarMap([$variableName => $expectedValue]);
        $rule = new GetOptionalString();
        $validator = new ParamsValuesImpl();
        $validationResult = $rule->process($variableName, $varMap, $validator);

        $this->assertEmpty($validationResult->getValidationProblems());
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }
}
