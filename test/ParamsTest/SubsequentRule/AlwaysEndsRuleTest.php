<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use ParamsTest\BaseTestCase;
use Params\ProcessRule\AlwaysEndsRule;
use Params\ParamsValuesImpl;
use VarMap\ArrayVarMap;

/**
 * @coversNothing
 */
class AlwaysEndsRuleTest extends BaseTestCase
{
    /**
     * @covers \Params\ProcessRule\AlwaysEndsRule
     */
    public function testUnknownFilterErrors()
    {
        $finalValue = 123;
        $rule = new AlwaysEndsRule($finalValue);
        $validator = new ParamsValuesImpl();
        $result = $rule->process('foo', new ArrayVarMap([]), $validator);

        $this->assertTrue($result->isFinalResult());
        $this->assertEquals($finalValue, $result->getValue());
        $this->assertCount(0, $result->getValidationProblems());
    }
}
