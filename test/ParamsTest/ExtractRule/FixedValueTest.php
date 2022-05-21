<?php

declare(strict_types = 1);

namespace ParamsTest\ExtractRule;

use Params\Messages;
use ParamsTest\BaseTestCase;
use Params\ExtractRule\FixedValue;
use Params\ProcessedValues;

use Params\DataStorage\TestArrayDataStorage;
use Params\DataStorage\ArrayDataStorage;

/**
 * @coversNothing
 */
class FixedValueTest extends BaseTestCase
{
    /**
     * @covers \Params\ExtractRule\FixedValue
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
     * @covers \Params\ExtractRule\FixedValue
     */
    public function coverage()
    {
        $value = 4;
        $rule = new FixedValue($value);

        $description = $this->applyRuleToDescription($rule);
    }
}
