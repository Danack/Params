<?php

declare(strict_types = 1);

namespace ParamsTest;

use Params\ExtractRule\GetInt;
use Params\InputParameter;
use Params\InputStorage\ArrayInputStorage;
use Params\ProcessRule\RangeIntValue;

/**
 * @coversNothing
 */
class InputParameterTest extends BaseTestCase
{
    /**
     * @covers \Params\InputParameter
     */
    public function testWorks()
    {
        $name = 'foo';
        $getIntRule = new GetInt();
        $processRule = new RangeIntValue(10, 20);

        $inputParamter = new InputParameter(
            $name,
            $getIntRule,
            $processRule
        );

        $this->assertSame($name, $inputParamter->getInputName());
        $this->assertSame($getIntRule, $inputParamter->getExtractRule());
        $this->assertSame([$processRule], $inputParamter->getProcessRules());
    }
}
