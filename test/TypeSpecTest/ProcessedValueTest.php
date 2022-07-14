<?php

declare(strict_types = 1);

namespace TypeSpecTest;

use TypeSpec\InputTypeSpec;
use TypeSpec\ProcessedValue;
use TypeSpec\ExtractRule\FixedValue;

/**
 * @coversNothing
 */
class ProcessedValueTest extends BaseTestCase
{

        /**
     * @covers \TypeSpec\ProcessedValue
     */
    public function testMissingGivesException()
    {
        $value = 5;
        $foo = new InputTypeSpec(
            'john',
            new FixedValue($value)
        );
        $processedValues = new ProcessedValue($foo, $value);
        $this->assertSame($value, $processedValues->getValue());
        $this->assertSame($foo, $processedValues->getInputTypeSpec());
    }
}
