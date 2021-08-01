<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\DataStorage\TestArrayDataStorage;
use Params\Messages;
use Params\ProcessedValues;
use Params\ProcessRule\ImagickIsRgbColor;
use ParamsTest\BaseTestCase;
use Params\OpenApi\OpenApiV300ParamDescription;
use Params\Exception\InvalidRulesException;

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
     * @covers \Params\ProcessRule\ImagickIsRgbColor
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
     * @covers \Params\ProcessRule\ImagickIsRgbColor
     */
    public function testOnlyString()
    {
        $testValue = 15;

        $rule = new ImagickIsRgbColor();
        $processedValues = new ProcessedValues();

        $dataStorage = TestArrayDataStorage::fromSingleValue('foo', $testValue);

        $this->expectException(InvalidRulesException::class);
        $this->expectExceptionMessageMatchesTemplateString(
            \Params\Messages::BAD_TYPE_FOR_STRING_PROCESS_RULE
        );

        $rule->process(
            $testValue, $processedValues, $dataStorage
        );
    }

    /**
     * @covers \Params\ProcessRule\ImagickIsRgbColor
     */
    public function testDescription()
    {
        $rule = new ImagickIsRgbColor();
        $description = $this->applyRuleToDescription($rule);
        $this->assertSame('color', $description->getFormat());
    }
}
