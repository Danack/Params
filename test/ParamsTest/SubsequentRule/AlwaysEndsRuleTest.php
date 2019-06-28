<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use ParamsTest\BaseTestCase;
use Params\SubsequentRule\AlwaysEndsRule;
use Params\ParamsValidator;
use VarMap\ArrayVarMap;

/**
 * @coversNothing
 */
class AlwaysEndsRuleTest extends BaseTestCase
{
    /**
     * @covers \Params\SubsequentRule\AlwaysEndsRule
     */
    public function testUnknownFilterErrors()
    {
        $finalValue = 123;
        $rule = new AlwaysEndsRule($finalValue);
        $validator = new ParamsValidator();
        $result = $rule->process('foo', new ArrayVarMap([]), $validator);

        $this->assertTrue($result->isFinalResult());
        $this->assertEquals($finalValue, $result->getValue());
        $this->assertNull($result->getProblemMessage());
    }
}
