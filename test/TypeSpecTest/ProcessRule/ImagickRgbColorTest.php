<?php

declare(strict_types=1);

namespace TypeSpecTest\ProcessRule;

use TypeSpec\DataStorage\TestArrayDataStorage;
use TypeSpec\Messages;
use TypeSpec\ProcessedValues;
use TypeSpec\ProcessRule\ImagickIsRgbColor;
use TypeSpecTest\BaseTestCase;
use TypeSpec\OpenApi\OpenApiV300ParamDescription;
use TypeSpec\Exception\InvalidRulesException;

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
     * @covers \TypeSpec\ProcessRule\ImagickIsRgbColor
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
     * @covers \TypeSpec\ProcessRule\ImagickIsRgbColor
     */
    public function testOnlyString()
    {
        $testValue = 15;

        $rule = new ImagickIsRgbColor();
        $processedValues = new ProcessedValues();

        $dataStorage = TestArrayDataStorage::fromSingleValue('foo', $testValue);

        $this->expectException(InvalidRulesException::class);
        $this->expectExceptionMessageMatchesTemplateString(
            \TypeSpec\Messages::BAD_TYPE_FOR_STRING_PROCESS_RULE
        );

        $rule->process(
            $testValue, $processedValues, $dataStorage
        );
    }

    /**
     * @covers \TypeSpec\ProcessRule\ImagickIsRgbColor
     */
    public function testDescription()
    {
        $rule = new ImagickIsRgbColor();
        $description = $this->applyRuleToDescription($rule);
        $this->assertSame('color', $description->getFormat());
    }
}
