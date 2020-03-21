<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use VarMap\ArrayVarMap;
use ParamsTest\BaseTestCase;
use Params\ExtractRule\GetOptionalString;
use Params\ParamsValidator;

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
        $validator = new ParamsValidator();

        $validationResult = $rule->process('foo', new ArrayVarMap([]), $validator);
        $this->assertEmpty($validationResult->getProblemMessages());
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
        $validator = new ParamsValidator();
        $validationResult = $rule->process($variableName, $varMap, $validator);

        $this->assertEmpty($validationResult->getProblemMessages());
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }
}
