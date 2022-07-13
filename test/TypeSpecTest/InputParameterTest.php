<?php

declare(strict_types = 1);

namespace TypeSpecTest;

use TypeSpec\ExtractRule\GetInt;
use TypeSpec\InputTypeSpec;
use TypeSpec\DataStorage\TestArrayDataStorage;
use TypeSpec\ProcessRule\RangeIntValue;

/**
 * @coversNothing
 */
class InputParameterTest extends BaseTestCase
{
    /**
     * @covers \TypeSpec\InputTypeSpec
     */
    public function testWorks()
    {
        $name = 'foo';
        $getIntRule = new GetInt();
        $processRule = new RangeIntValue(10, 20);

        $inputParamter = new InputTypeSpec(
            $name,
            $getIntRule,
            $processRule
        );

        $this->assertSame($name, $inputParamter->getInputName());
        $this->assertSame($getIntRule, $inputParamter->getExtractRule());
        $this->assertSame([$processRule], $inputParamter->getProcessRules());
    }
}
