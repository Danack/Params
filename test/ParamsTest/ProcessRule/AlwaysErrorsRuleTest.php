<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Type\ProcessedValues;
use ParamsTest\BaseTestCase;
use Type\ProcessRule\AlwaysErrorsRule;
use Type\OpenApi\OpenApiV300ParamDescription;
use Type\DataStorage\TestArrayDataStorage;

/**
 * @coversNothing
 */
class AlwaysErrorsRuleTest extends BaseTestCase
{
    /**
     * @covers \Type\ProcessRule\AlwaysErrorsRule
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
     * @covers \Type\ProcessRule\AlwaysErrorsRule
     */
    public function testCoverage()
    {
        $message = 'test message';
        $rule = new AlwaysErrorsRule($message);
        $description = $this->applyRuleToDescription($rule);
    }
}
