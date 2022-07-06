<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Type\DataStorage\TestArrayDataStorage;
use ParamsTest\BaseTestCase;
use Type\ProcessRule\AlwaysEndsRule;
use Type\ProcessedValues;

/**
 * @coversNothing
 */
class AlwaysEndsRuleTest extends BaseTestCase
{
    /**
     * @covers \Type\ProcessRule\AlwaysEndsRule
     */
    public function testWorks()
    {
        $finalValue = 123;
        $rule = new AlwaysEndsRule($finalValue);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);
        $result = $rule->process(
            $unused_input = 4,
            $processedValues,
            $dataStorage
        );

        $this->assertNoProblems($result);
        $this->assertTrue($result->isFinalResult());
        $this->assertEquals($finalValue, $result->getValue());
    }

    /**
     * @covers \Type\ProcessRule\AlwaysEndsRule
     */
    public function testDescription()
    {
        $finalValue = 123;
        $rule = new AlwaysEndsRule($finalValue);
        $description = $this->applyRuleToDescription($rule);
        // nothing to assert.
    }
}
