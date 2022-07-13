<?php

declare(strict_types = 1);

namespace TypeSpecTest;

use ThreeColors;
use TypeSpec\InputTypeSpec;
use TypeSpec\ExtractRule\GetStringOrDefault;
use TypeSpec\ProcessRule\ImagickIsRgbColor;

/**
 * @coversNothing
 */
class InputParameterListFromAttributesTest extends BaseTestCase
{
    /**
     * @covers \TypeSpec\InputTypeSpecListFromAttributes
     * @covers \ThreeColors
     */
    function testWorks()
    {
        $inputParameters = ThreeColors::getInputTypeSpecList();

        foreach ($inputParameters as $inputParameter) {
            $this->assertInstanceOf(InputTypeSpec::class, $inputParameter);
            $this->assertInstanceOf(GetStringOrDefault::class, $inputParameter->getExtractRule());

            $processRules = $inputParameter->getProcessRules();
            $this->assertCount(1, $processRules);
            $processRule = $processRules[0];
            $this->assertInstanceOf(ImagickIsRgbColor::class, $processRule);
        }
    }
}
