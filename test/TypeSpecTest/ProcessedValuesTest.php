<?php

declare(strict_types = 1);

namespace TypeSpecTest;

use TypeSpec\ProcessedValues;
use TypeSpec\Exception\LogicException;
use TypeSpec\InputTypeSpec;
use TypeSpec\ExtractRule\GetStringOrDefault;
use TypeSpec\ProcessedValue;

/**
 * @coversNothing
 */
class ProcessedValuesTest extends BaseTestCase
{
    /**
     * @covers \TypeSpec\ProcessedValues
     */
    public function testWorks()
    {
        $data = [
            'foo' => 'bar',
            'zebransky' => 'zoqfotpik'
        ];

        $processedValues = createProcessedValuesFromArray($data);
        $this->assertSame($data, $processedValues->getAllValues());
        $this->assertSame('bar', $processedValues->getValue('foo'));

        $this->assertTrue($processedValues->hasValue('foo'));
        $this->assertFalse($processedValues->hasValue('bad_name'));
    }

        /**
     * @covers \TypeSpec\ProcessedValues
     */
    public function testMissingGivesException()
    {
        $processedValues = createProcessedValuesFromArray([]);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessageMatchesTemplateString(LogicException::MISSING_VALUE);
        $processedValues->getValue('john');
    }


//    /**
//     * @covers \Params\ProcessedValues
//     */
//    public function testBadArrayException()
//    {
//        $this->expectException(LogicException::class);
//        $this->expectExceptionMessageMatchesTemplateString(LogicException::ONLY_KEYS);
//        $processedValues = ProcessedValues::fromArray(['foo']);
//    }


    public function testGetCorrectTarget()
    {
        $inputParameter = new InputTypeSpec(
            'background_color',
            new GetStringOrDefault('red')
        );

        $inputParameter->setTargetParameterName('backgroundColor');
        $processedValue = new ProcessedValue($inputParameter, 'red');
        $processedValues = ProcessedValues::fromArray([$processedValue]);

        [$value_for_target, $available] = $processedValues->getValueForTargetProperty('backgroundColor');
        $this->assertTrue($available);
        $this->assertSame('red', $value_for_target);
    }
}
