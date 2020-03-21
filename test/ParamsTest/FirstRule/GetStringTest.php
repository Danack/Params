<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use VarMap\ArrayVarMap;
use ParamsTest\BaseTestCase;
use Params\ExtractRule\GetString;
use Params\ParamsValidator;

/**
 * @coversNothing
 */
class GetStringTest extends BaseTestCase
{
    /**
     * @covers \Params\ExtractRule\GetString
     */
    public function testMissingGivesError()
    {
        $rule = new GetString();
        $validator = new ParamsValidator();
        $validationResult = $rule->process('foo', new ArrayVarMap([]), $validator);
        $this->assertNotNull($validationResult->getProblemMessages());
    }

    /**
     * @covers \Params\ExtractRule\GetString
     */
    public function testValidation()
    {
        $variableName = 'foo';
        $expectedValue = 'bar';

        $rule = new GetString();
        $validator = new ParamsValidator();
        $validationResult = $rule->process(
            $variableName,
            new ArrayVarMap([$variableName => $expectedValue]),
            $validator
        );

        $this->assertEmpty($validationResult->getProblemMessages());
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }
}
