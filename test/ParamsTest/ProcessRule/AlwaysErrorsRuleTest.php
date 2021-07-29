<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\ProcessedValues;
use ParamsTest\BaseTestCase;
use Params\ProcessRule\AlwaysErrorsRule;
use Params\OpenApi\OpenApiV300ParamDescription;
use Params\DataStorage\TestArrayDataStorage;

/**
 * @coversNothing
 */
class AlwaysErrorsRuleTest extends BaseTestCase
{
    /**
     * @covers \Params\ProcessRule\AlwaysErrorsRule
     */
    public function testWorks()
    {
        $message = 'test message';
        $rule = new AlwaysErrorsRule($message);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValue('foo', 'bar');
        $result = $rule->process(
            $unused_input = 5,
            $processedValues,
            $dataStorage
        );

        $this->assertCount(1, $result->getValidationProblems());
        $this->assertValidationProblem(
            '/foo',
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
        $description = $this->applyRuleToDescription($rule);
    }
}
