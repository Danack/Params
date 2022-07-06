<?php

declare(strict_types = 1);

namespace ParamsTest;

use ThreeColors;
use Type\PropertyDefinition;
use Type\ExtractRule\GetStringOrDefault;
use Type\ProcessRule\ImagickIsRgbColor;

/**
 * @coversNothing
 */
class InputParameterListFromAttributesTest extends BaseTestCase
{
    /**
     * @covers \Type\InputParameterListFromAttributes
     * @covers \ThreeColors
     */
    function testWorks()
    {
        $inputParameters = ThreeColors::getPropertyDefinitionList();

        foreach ($inputParameters as $inputParameter) {
            $this->assertInstanceOf(PropertyDefinition::class, $inputParameter);
            $this->assertInstanceOf(GetStringOrDefault::class, $inputParameter->getExtractRule());

            $processRules = $inputParameter->getProcessRules();
            $this->assertCount(1, $processRules);
            $processRule = $processRules[0];
            $this->assertInstanceOf(ImagickIsRgbColor::class, $processRule);
        }
    }
}
