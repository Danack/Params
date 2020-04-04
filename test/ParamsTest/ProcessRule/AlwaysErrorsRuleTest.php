<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\DataLocator\StandardDataLocator;
use Params\ParamsValuesImpl;
use Params\ProcessRule\MaximumCount;
use ParamsTest\BaseTestCase;
use Params\ProcessRule\AlwaysErrorsRule;
use Params\OpenApi\OpenApiV300ParamDescription;
use Params\Path;
use function Params\createPath;
use Params\DataLocator\EmptyDataLocator;

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
        $dataLocator = EmptyDataLocator::fromPath(['foo']);
        $result = $rule->process(
            Path::fromName('foo'),
            5,
            $validator,
            $dataLocator
        );

        $this->assertCount(1, $result->getValidationProblems());
        $this->assertValidationProblem(
            createPath(['name' => 'foo']),
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
