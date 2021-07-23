<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\InputStorage\ArrayInputStorage;
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
    public function testWorks()
    {
        $finalValue = 123;
        $rule = new AlwaysEndsRule($finalValue);
        $processedValues = new ProcessedValues();
        $dataLocator = ArrayInputStorage::fromArraySetFirstValue([]);
        $result = $rule->process(
            $unused_input = 4,
            $processedValues,
            $dataLocator
        );

        $this->assertNoProblems($result);
        $this->assertTrue($result->isFinalResult());
        $this->assertEquals($finalValue, $result->getValue());
    }

    /**
     * @covers \Params\ProcessRule\AlwaysEndsRule
     */
    public function testDescription()
    {
        $finalValue = 123;
        $rule = new AlwaysEndsRule($finalValue);
        $description = $this->applyRuleToDescription($rule);
        // nothing to assert.
    }
}
