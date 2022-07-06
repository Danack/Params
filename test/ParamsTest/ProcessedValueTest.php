<?php

declare(strict_types = 1);

namespace ParamsTest;

use Type\PropertyDefinition;
use Type\ProcessedValue;
use Type\ExtractRule\FixedValue;

/**
 * @coversNothing
 */
class ProcessedValueTest extends BaseTestCase
{

        /**
     * @covers \Type\ProcessedValue
     */
    public function testMissingGivesException()
    {
        $value = 5;
        $foo = new PropertyDefinition(
            'john',
            new FixedValue($value)
        );
        $processedValues = new ProcessedValue($foo, $value);
        $this->assertSame($value, $processedValues->getValue());
        $this->assertSame($foo, $processedValues->getParam());
    }
}
