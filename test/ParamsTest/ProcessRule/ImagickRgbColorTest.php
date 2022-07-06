<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Type\DataStorage\TestArrayDataStorage;
use Type\Messages;
use Type\ProcessedValues;
use Type\ProcessRule\ImagickIsRgbColor;
use ParamsTest\BaseTestCase;
use Type\OpenApi\OpenApiV300ParamDescription;
use Type\Exception\InvalidRulesException;

/**
 * @coversNothing
 */
class ImagickRgbColorTest extends BaseTestCase
{

    public function provideTestWorks()
    {
        yield ["DarkSeaGreen3"];
        yield ["DodgerBlue2"];
        yield ['rgb(100,100,100)'];
    }

    /**
     * @dataProvider provideTestWorks
     * @covers \Type\ProcessRule\ImagickIsRgbColor
     */
    public function testWorks(string $testValue)
    {
        $rule = new ImagickIsRgbColor();
        $processedValues = new ProcessedValues();

        $dataStorage = TestArrayDataStorage::fromSingleValue('foo', $testValue);

        $validationResult = $rule->process(
            $testValue, $processedValues, $dataStorage
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($testValue, $validationResult->getValue());
    }

    /**
     * @covers \Type\ProcessRule\ImagickIsRgbColor
     */
    public function testOnlyString()
    {
        $testValue = 15;

        $rule = new ImagickIsRgbColor();
        $processedValues = new ProcessedValues();

        $dataStorage = TestArrayDataStorage::fromSingleValue('foo', $testValue);

        $this->expectException(InvalidRulesException::class);
        $this->expectExceptionMessageMatchesTemplateString(
            \Type\Messages::BAD_TYPE_FOR_STRING_PROCESS_RULE
        );

        $rule->process(
            $testValue, $processedValues, $dataStorage
        );
    }

    /**
     * @covers \Type\ProcessRule\ImagickIsRgbColor
     */
    public function testDescription()
    {
        $rule = new ImagickIsRgbColor();
        $description = $this->applyRuleToDescription($rule);
        $this->assertSame('color', $description->getFormat());
    }
}
