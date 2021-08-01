<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\DataStorage\TestArrayDataStorage;
use Params\Messages;
use Params\ProcessedValues;
use Params\ProcessRule\IsRgbColor;
use ParamsTest\BaseTestCase;
use Params\OpenApi\OpenApiV300ParamDescription;
use Params\Exception\InvalidRulesException;

/**
 * @coversNothing
 */
class IsRgbColorTest extends BaseTestCase
{

    public function provideTestWorks()
    {
        yield ['rgb(100,100,100)'];
        yield ['rgb(100, 100, 100)'];

//        yield ['#0f38'];
//        yield ['#00ff3388'];
//        yield ['#ff0000ff'];
//        yield ['#ff0000'];
    }


//rgb(255, 0, 0)                 range 0 - 255
//rgba(255, 0, 0, 1.0)           the same, with an explicit alpha value
//rgb(100%, 0%, 0%)              range 0.0% - 100.0%
//rgba(100%, 0%, 0%, 1.0)        the same, with an explicit alpha value

//gray50            near mid gray
//gray(127)         near mid gray
//gray(50%)         mid gray
//graya(50%, 0.5)   semi-transparent mid gray

//hsb(0,   100%,  100%)    or    hsb(0,   255,   255)       full red
//hsb(120, 100%,  100%)    or    hsb(120, 255,   255)       full green
//hsb(120, 100%,  75%)     or    hsb(120, 255,   191.25)    medium green
//hsb(120, 100%,  50%)     or    hsb(120, 255,   127.5)     dark green
//hsb(120, 100%,  25%)     or    hsb(120, 255,   63.75)     very dark green
//hsb(120, 50%,   50%)     or    hsb(120, 127.5, 127.5)     pastel green

//hsl(0,   100%,  50%)     or    hsl(0,   255,   127.5)     full red
//hsl(120, 100%,  100%)    or    hsl(120, 255,   255)       white
//hsl(120, 100%,  75%)     or    hsl(120, 255,   191.25)    pastel green
//hsl(120, 100%,  50%)     or    hsl(120, 255,   127.5)     full green
//hsl(120, 100%,  25%)     or    hsl(120, 255,   63.75)     dark green
//hsl(120, 50%,   50%)     or    hsl(120, 127.5, 127.5)     medium green

//hsb(120, 100%,  100%)              full green in hsb
//hsba(120, 100%,  100%,  1.0)       the same, with an alpha value of 1.0
//hsb(120, 255,  255)                full green in hsb
//hsba(120, 255,  255,  1.0)         the same, with an alpha value of 1.0

//hsl(120, 100%,  50%)               full green in hsl
//hsla(120, 100%,  50%,  1.0)        the same, with an alpha value of 1.0
//hsl(120, 255,  127.5)              full green in hsl
//hsla(120, 255,  127.5,  1.0)       the same, with an alpha value of 1.0

//cielab(62.253188, 23.950124, 48.410653)

//icc-color(cmyk, 0.11, 0.48, 0.83, 0.00)  cymk
//icc-color(rgb, 1, 0, 0)                  linear rgb
//icc-color(rgb, red)                      linear rgb
//icc-color(lineargray, 0.5)               linear gray
//icc-color(srgb, 1, 0, 0)                 non-linear rgb
//icc-color(srgb, red)                     non-linear rgb
//icc-color(gray, 0.5)                     non-linear gray

//device-gray(0.5)
//device-rgb(0.5, 1.0, 0.0)
//device-cmyk(0.11, 0.48, 0.83, 0.00)



//The sRGB, CMYK, HSL and HSB color models are used in numerical color specifications. These examples all specify the same red sRGB color:

//#f00                      #rgb
//#ff0000                   #rrggbb
//#ff0000ff                 #rrggbbaa
//#ffff00000000             #rrrrggggbbbb
//#ffff00000000ffff         #rrrrggggbbbbaaaa
//rgb(255, 0, 0)            an integer in the range 0—255 for each component
//rgb(100.0%, 0.0%, 0.0%)   a float in the range 0—100% for each component





//"blue", "#0000ff", "rgb(0,0,255)", "cmyk(100,100,100,10)", etc.).


    // rgb(), rgba(), hsl(), and hsla() have all gained a new syntax
    // consisting of space-separated arguments and an optional slash-separated
    // opacity. All the color functions use this syntax form now, in keeping
    // with CSS’s functional-notation design principles.



    /**
     * @dataProvider provideTestWorks
     * @covers \Params\ProcessRule\IsRgbColor
     */
    public function testWorks(string $testValue)
    {
        $rule = new IsRgbColor();
        $processedValues = new ProcessedValues();

        $dataStorage = TestArrayDataStorage::fromSingleValue('foo', $testValue);

        $validationResult = $rule->process(
            $testValue, $processedValues, $dataStorage
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($testValue, $validationResult->getValue());
    }


    public function testOnlyString()
    {
        $testValue = 15;

        $rule = new IsRgbColor();
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


    public function provideTestErrors()
    {
        // TODO - these should give a precise position of the error.
        yield ['rgb("100,"100","100")'];
    }

    /**
     * @dataProvider provideTestErrors
     * @covers \Params\ProcessRule\IsRgbColor
     */
    public function testErrors($testValue)
    {
        $rule = new IsRgbColor();
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValue('foo', $testValue);

        $validationResult = $rule->process(
            $testValue,
            $processedValues,
            $dataStorage
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            IsRgbColor::BAD_COLOR_STRING,
            $validationResult->getValidationProblems()
        );
    }


    /**
     * @covers \Params\ProcessRule\IsRgbColor
     */
    public function testDescription()
    {
        $rule = new IsRgbColor();
        $description = $this->applyRuleToDescription($rule);
        $this->assertSame('color', $description->getFormat());
    }
}
