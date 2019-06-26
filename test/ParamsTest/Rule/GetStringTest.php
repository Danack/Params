<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use VarMap\ArrayVarMap;
use ParamsTest\BaseTestCase;
use Params\FirstRule\GetString;
use Params\ParamsValidator;

/**
 * @coversNothing
 */
class GetStringTest extends BaseTestCase
{
    /**
     * @covers \Params\FirstRule\GetString
     */
    public function testMissingGivesError()
    {
        $rule = new GetString();
        $validator = new ParamsValidator();
        $validationResult = $rule->process('foo', new ArrayVarMap([]), $validator);
        $this->assertNotNull($validationResult->getProblemMessage());
    }

    /**
     * @covers \Params\FirstRule\GetString
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

        $this->assertNull($validationResult->getProblemMessage());
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }
}
