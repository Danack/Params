<?php

declare(strict_types=1);

namespace ParamsTest;

use Params\ExtractRule\GetStringOrDefault;
use Params\InputParameter;
use Params\Messages;
use Params\ProcessRule\ImagickRgbColorRule;
use VarMap\ArrayVarMap;
use Params\Exception\AnnotationClassDoesNotExistException;
use Params\Exception\IncorrectNumberOfParamsException;
use Params\Exception\NoConstructorException;
use Params\Exception\MissingConstructorParameterNameException;
use Params\Exception\PropertyHasMultipleParamAnnotationsException;
use function \Params\createTypeFromAnnotations;

/**
 * @coversNothing
 * TODO - this class duplicates some tests that should be elsewhere.
 */
class ParamAnnotationsTest extends BaseTestCase
{
    public function testCreateWorks()
    {
        $varMap = new ArrayVarMap([
            'background_color' => 'red',
            'stroke_color' => 'rgb(255, 0, 255)',
            'fill_color' => 'white',
        ]);

        $result = createTypeFromAnnotations($varMap, \ThreeColors::class);

        $this->assertInstanceOf(\ThreeColors::class, $result);
    }

    public function testCreateFromVarMapWorks()
    {
        $varMap = new ArrayVarMap([
            'background_color' => 'red',
            'stroke_color' => 'rgb(255, 0, 255)',
            'fill_color' => 'white',
        ]);

        $threeColors = \ThreeColors::createFromVarMap($varMap);
        $this->assertInstanceOf(\ThreeColors::class, $threeColors);
    }

    public function testGetParamsFromAnnotation()
    {
        $varMap = new ArrayVarMap([
            'background_color' => 'red',
            'stroke_color' => 'rgb(255, 0, 255)',
            'fill_color' => 'white',
        ]);

        $threeColors = \ThreeColors::createFromVarMap($varMap);
        $this->assertInstanceOf(\ThreeColors::class, $threeColors);

        $inputParameters = $threeColors::getInputParameterList();

        $this->assertCount(3, $inputParameters);

        $namesAndDefaults = [
            ['background_color', 'rgb(225, 225, 225)'],
            ['stroke_color', 'rgb(0, 0, 0)'],
            ['fill_color', 'DodgerBlue2'],
        ];

        $count = 0;
        foreach ($inputParameters as $inputParameter) {
            $expectedName = $namesAndDefaults[$count][0];
            $expectedDefault = $namesAndDefaults[$count][1];

            $this->assertInstanceOf(InputParameter::class, $inputParameter);

            $this->assertSame(
                $expectedName,
                $inputParameter->getInputName()
            );

            $extractRule = $inputParameter->getExtractRule();
            $this->assertInstanceOf(
                GetStringOrDefault::class,
                $extractRule
            );

            /** @var GetStringOrDefault $extractRule */
            $this->assertSame(
                $expectedDefault,
                $extractRule->getDefault()
            );

            $count += 1;
        }
    }


    public function testMissingConstructorParamErrors()
    {
        $varMap = new ArrayVarMap([
            'background_color' => 'red',
            'stroke_color' => 'rgb(255, 0, 255)',
            'fill_color' => 'white',
        ]);

        // \Params\Exception\IncorrectNumberOfParamsException::wrongNumber
        // TODO - set expected exception
        $this->expectException(IncorrectNumberOfParamsException::class);
        createTypeFromAnnotations($varMap, \ThreeColorsMissingConstructorParam::class);
    }

    public function testMissingPropertyParamErrors()
    {
        $varMap = new ArrayVarMap([
            'background_color' => 'red',
            'stroke_color' => 'rgb(255, 0, 255)',
            'fill_color' => 'white',
        ]);

        $this->expectException(IncorrectNumberOfParamsException::class);
        createTypeFromAnnotations($varMap, \ThreeColorsMissingPropertyParam::class);
    }

    public function testMissingConstructor()
    {
        $varMap = new ArrayVarMap([
            'background_color' => 'red',
            'stroke_color' => 'rgb(255, 0, 255)',
            'fill_color' => 'white',
        ]);

        $this->expectException(NoConstructorException::class);
        $this->expectExceptionMessageMatchesTemplateString(
            Messages::CLASS_LACKS_CONSTRUCTOR
        );

        createTypeFromAnnotations($varMap, \ThreeColorsNoConstructor::class);
    }


    public function testNoPublicConstructor()
    {
        $varMap = new ArrayVarMap([
            'background_color' => 'red',
            'stroke_color' => 'rgb(255, 0, 255)',
            'fill_color' => 'white',
        ]);

        $this->expectException(NoConstructorException::class);

        $this->expectExceptionMessageMatchesTemplateString(
            Messages::CLASS_LACKS_PUBLIC_CONSTRUCTOR
        );

        createTypeFromAnnotations($varMap, \ThreeColorsPrivateConstructor::class);
    }


    public function testIncorrectContructorParameterName()
    {
        $varMap = new ArrayVarMap([
            'background_color' => 'red',
            'stroke_color' => 'rgb(255, 0, 255)',
            'fill_color' => 'white',
        ]);

        $this->expectException(MissingConstructorParameterNameException::class);

        createTypeFromAnnotations($varMap, \ThreeColorsIncorrectParamName::class);
    }

    public function testOneParamWithOneOtherPropertyName()
    {
        $varMap = new ArrayVarMap([
            'background_color' => 'red',
        ]);

//        $this->expectException(MissingConstructorParameterNameException::class);
        createTypeFromAnnotations($varMap, \OneColor::class);
    }

    public function testOneParamName()
    {
        $varMap = new ArrayVarMap([
            'background_color' => 'red',
        ]);

        $result = createTypeFromAnnotations(
            $varMap,
            \OneColorWithOtherAnnotationThatIsNotAParam::class
        );
        $this->assertInstanceOf(
            \OneColorWithOtherAnnotationThatIsNotAParam::class,
            $result
        );
    }


    public function testNonExistentParamErrorsSensibly()
    {
        $varMap = new ArrayVarMap([
            'background_color' => 'red',
        ]);

        $this->expectException(AnnotationClassDoesNotExistException::class);
        $this->expectExceptionMessageMatchesTemplateString(
            Messages::PROPERTY_ANNOTATION_DOES_NOT_EXIST
        );

        $this->expectExceptionMessageMatches('#.*stroke_color.*#iu');

        createTypeFromAnnotations(
            $varMap,
            \OneColorWithOtherAnnotationThatDoesNotExist::class
        );
    }



    public function testMultipleParamsErrors()
    {
        $varMap = new ArrayVarMap([
            'background_color' => 'red',
        ]);

        $this->expectException(PropertyHasMultipleParamAnnotationsException::class);
        $this->expectExceptionMessageMatches('#.*background_color.*#iu');

        createTypeFromAnnotations($varMap, \MultipleParamAnnotations::class);
    }
}
