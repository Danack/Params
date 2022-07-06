<?php

declare(strict_types = 1);

namespace ParamsTest\ExtractRule;

use Type\Messages;
use ParamsTest\BaseTestCase;
use Type\ExtractRule\FixedValue;
use Type\ProcessedValues;

use Type\DataStorage\TestArrayDataStorage;
use Type\DataStorage\ArrayDataStorage;

/**
 * @coversNothing
 */
class FixedValueTest extends BaseTestCase
{
    /**
     * @covers \Type\ExtractRule\FixedValue
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
     * @covers \Type\ExtractRule\FixedValue
     */
    public function coverage()
    {
        $value = 4;
        $rule = new FixedValue($value);

        $description = $this->applyRuleToDescription($rule);
    }
}
