<?php

declare(strict_types = 1);

namespace TypeSpecTest\ExtractRule;

use TypeSpec\Messages;
use TypeSpecTest\BaseTestCase;
use TypeSpec\ExtractRule\FixedValue;
use TypeSpec\ProcessedValues;

use TypeSpec\DataStorage\TestArrayDataStorage;
use TypeSpec\DataStorage\ArrayDataStorage;

/**
 * @coversNothing
 */
class FixedValueTest extends BaseTestCase
{
    /**
     * @covers \TypeSpec\ExtractRule\FixedValue
     */
    public function testMissingGivesError()
    {
        $value = 4;
        $rule = new FixedValue($value);

        $processedValues = new ProcessedValues();
        $validationResult = $rule->process(
            $processedValues,
            ArrayDataStorage::fromArray([])
        );

        $this->assertSame($value, $validationResult->getValue());
        $this->assertFalse($validationResult->anyErrorsFound());
    }

    /**
     * @covers \TypeSpec\ExtractRule\FixedValue
     */
    public function coverage()
    {
        $value = 4;
        $rule = new FixedValue($value);

        $description = $this->applyRuleToDescription($rule);
    }
}
