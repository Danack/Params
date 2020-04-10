<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\DataLocator\DataStorage;
use Params\ProcessedValuesImpl;
use Params\ProcessRule\MaximumCount;
use ParamsTest\BaseTestCase;
use Params\ProcessRule\AlwaysErrorsRule;
use Params\OpenApi\OpenApiV300ParamDescription;
use Params\Path;
use function Params\createPath;
use Params\DataLocator\EmptyInputStorageAye;

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
        $processedValues = new ProcessedValuesImpl();
        $dataLocator = EmptyInputStorageAye::fromPath(['foo']);
        $result = $rule->process(
            $unused_input = 5,
            $processedValues,
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
