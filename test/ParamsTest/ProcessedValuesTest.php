<?php

declare(strict_types = 1);

namespace ParamsTest;

use Params\ProcessedValues;
use Params\Exception\LogicException;

/**
 * @coversNothing
 */
class ProcessedValuesTest extends BaseTestCase
{
    /**
     * @covers \Params\ProcessedValues
     */
    public function testWorks()
    {
        $data = [
            'foo' => 'bar',
            'zebransky' => 'zoqfotpik'
        ];

        $processedValues = ProcessedValues::fromArray($data);
        $this->assertSame($data, $processedValues->getAllValues());
        $this->assertSame('bar', $processedValues->getValue('foo'));

        $this->assertTrue($processedValues->hasValue('foo'));
        $this->assertFalse($processedValues->hasValue('bad_name'));
    }

        /**
     * @covers \Params\ProcessedValues
     */
    public function testMissingGivesException()
    {
        $processedValues = ProcessedValues::fromArray([]);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessageMatchesTemplateString(LogicException::MISSING_VALUE);
        $processedValues->getValue('john');
    }


    /**
     * @covers \Params\ProcessedValues
     */
    public function testBadArrayException()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessageMatchesTemplateString(LogicException::ONLY_KEYS);
        $processedValues = ProcessedValues::fromArray(['foo']);
    }
}
