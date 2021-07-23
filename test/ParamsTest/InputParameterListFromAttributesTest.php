<?php

declare(strict_types = 1);

namespace ParamsTest;

use ThreeColors;
use Params\InputParameter;
use Params\ExtractRule\GetStringOrDefault;
use Params\ProcessRule\ImagickRgbColorRule;

/**
 * @coversNothing
 */
class InputParameterListFromAttributesTest extends BaseTestCase
{
    /**
     * @covers \Params\InputParameterListFromAttributes
     * @covers \ThreeColors
     */
    function testWorks()
    {
        $inputParameters = ThreeColors::getInputParameterList();

        foreach ($inputParameters as $inputParameter) {
            $this->assertInstanceOf(InputParameter::class, $inputParameter);
            $this->assertInstanceOf(GetStringOrDefault::class, $inputParameter->getExtractRule());

            $processRules = $inputParameter->getProcessRules();
            $this->assertCount(1, $processRules);
            $processRule = $processRules[0];
            $this->assertInstanceOf(ImagickRgbColorRule::class, $processRule);
        }
    }
}
