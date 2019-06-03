<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use ParamsTest\BaseTestCase;
use Params\Rule\AlwaysErrorsRule;
use Params\OpenApi\OpenApiV300ParamDescription;

/**
 * @coversNothing
 */
class AlwaysErrorsRuleTest extends BaseTestCase
{
    /**
     * @covers \Params\Rule\AlwaysErrorsRule
     */
    public function testUnknownFilterErrors()
    {
        $message = 'test message';
        $rule = new AlwaysErrorsRule($message);

        $result = $rule('foo', 5);

        $this->assertEquals($message, $result->getProblemMessage());
        $this->assertTrue($result->isFinalResult());
        $this->assertNull($result->getValue());
    }

    /**
     * @covers \Params\Rule\AlwaysErrorsRule
     */
    public function testCoverage()
    {
        $message = 'test message';
        $rule = new AlwaysErrorsRule($message);

        $paramDescription = new OpenApiV300ParamDescription();

        $rule->updateParamDescription($paramDescription);
    }
}
