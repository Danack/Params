<?php

declare(strict_types=1);

namespace TypeSpecTest\ProcessRule;

use TypeSpec\DataStorage\TestArrayDataStorage;
use TypeSpecTest\BaseTestCase;
use TypeSpec\ProcessRule\AlwaysEndsRule;
use TypeSpec\ProcessedValues;

/**
 * @coversNothing
 */
class AlwaysEndsRuleTest extends BaseTestCase
{
    /**
     * @covers \TypeSpec\ProcessRule\AlwaysEndsRule
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
     * @covers \TypeSpec\ProcessRule\AlwaysEndsRule
     */
    public function testDescription()
    {
        $finalValue = 123;
        $rule = new AlwaysEndsRule($finalValue);
        $description = $this->applyRuleToDescription($rule);
        // nothing to assert.
    }
}
