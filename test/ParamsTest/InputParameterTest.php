<?php

declare(strict_types = 1);

namespace ParamsTest;

use Type\ExtractRule\GetInt;
use Type\PropertyDefinition;
use Type\DataStorage\TestArrayDataStorage;
use Type\ProcessRule\RangeIntValue;

/**
 * @coversNothing
 */
class InputParameterTest extends BaseTestCase
{
    /**
     * @covers \Type\PropertyDefinition
     */
    public function testWorks()
    {
        $name = 'foo';
        $getIntRule = new GetInt();
        $processRule = new RangeIntValue(10, 20);

        $inputParamter = new PropertyDefinition(
            $name,
            $getIntRule,
            $processRule
        );

        $this->assertSame($name, $inputParamter->getInputName());
        $this->assertSame($getIntRule, $inputParamter->getExtractRule());
        $this->assertSame([$processRule], $inputParamter->getProcessRules());
    }
}
