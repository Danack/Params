<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use Params\ParamsValuesImpl;
use Params\ProcessRule\MaximumCount;
use ParamsTest\BaseTestCase;
use Params\ProcessRule\AlwaysErrorsRule;
use Params\OpenApi\OpenApiV300ParamDescription;

/**
 * @coversNothing
 */
class AlwaysErrorsRuleTest extends BaseTestCase
{
    /**
     * @covers \Params\ProcessRule\AlwaysErrorsRule
     */
    public function testUnknownFilterErrors()
    {
        $message = 'test message';
        $rule = new AlwaysErrorsRule($message);
        $validator = new ParamsValuesImpl();

        $result = $rule->process('foo', 5, $validator);

        $this->assertCount(1, $result->getValidationProblems());
        $this->assertValidationProblem(
            'foo',
            $message,
            $result->getValidationProblems()
        );

        $this->assertTrue($result->isFinalResult());
        $this->assertNull($result->getValue());
    }

    /**
     * @covers \Params\ProcessRule\AlwaysErrorsRule
     */
    public function testCoverage()
    {
        $message = 'test message';
        $rule = new AlwaysErrorsRule($message);

        $paramDescription = new OpenApiV300ParamDescription('John');

        $rule->updateParamDescription($paramDescription);
    }
}
