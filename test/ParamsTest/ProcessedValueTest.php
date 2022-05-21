<?php

declare(strict_types = 1);

namespace ParamsTest;

use Params\InputParameter;
use Params\ProcessedValue;
use Params\ExtractRule\FixedValue;

/**
 * @coversNothing
 */
class ProcessedValueTest extends BaseTestCase
{

        /**
     * @covers \Params\ProcessedValue
     */
    public function testMissingGivesException()
    {
        $value = 5;
        $foo = new InputParameter(
            'john',
            new FixedValue($value)
        );
        $processedValues = new ProcessedValue($foo, $value);
        $this->assertSame($value, $processedValues->getValue());
        $this->assertSame($foo, $processedValues->getParam());
    }
}
