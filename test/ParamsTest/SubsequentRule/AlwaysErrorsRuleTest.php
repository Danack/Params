<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use Params\ParamsValidator;
use ParamsTest\BaseTestCase;
use Params\SubsequentRule\AlwaysErrorsRule;
use Params\OpenApi\OpenApiV300ParamDescription;

/**
 * @coversNothing
 */
class AlwaysErrorsRuleTest extends BaseTestCase
{
    /**
     * @covers \Params\SubsequentRule\AlwaysErrorsRule
     */
    public function testUnknownFilterErrors()
    {
        $message = 'test message';
        $rule = new AlwaysErrorsRule($message);
        $validator = new ParamsValidator();

        $result = $rule->process('foo', 5, $validator);

        $this->assertEquals($message, $result->getProblemMessages()['/foo']);
        $this->assertTrue($result->isFinalResult());
        $this->assertNull($result->getValue());
    }

    /**
     * @covers \Params\SubsequentRule\AlwaysErrorsRule
     */
    public function testCoverage()
    {
        $message = 'test message';
        $rule = new AlwaysErrorsRule($message);

        $paramDescription = new OpenApiV300ParamDescription();

        $rule->updateParamDescription($paramDescription);
    }
}
