<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\DataLocator\DataStorage;
use Params\OpenApi\OpenApiV300ParamDescription;
use Params\ProcessRule\BoolInput;
use ParamsTest\BaseTestCase;
use Params\ProcessRule\AlwaysEndsRule;
use Params\ProcessedValues;

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
        $processedValues = new ProcessedValues();
        $dataLocator = DataStorage::fromArraySetFirstValue([]);
        $result = $rule->process(
            $unused_input = 4,
            $processedValues,
            $dataLocator
        );

        $this->assertNoValidationProblems($result->getValidationProblems());
        $this->assertTrue($result->isFinalResult());
        $this->assertEquals($finalValue, $result->getValue());
    }

    /**
     * @covers \Params\ProcessRule\AlwaysEndsRule
     */
    public function testDescription()
    {
        $description = new OpenApiV300ParamDescription('John');
        $finalValue = 123;
        $rule = new AlwaysEndsRule($finalValue);
        $rule->updateParamDescription($description);
        // nothing to assert.
    }
}
