<?php

declare(strict_types=1);

namespace ParamsTest;

use Params\Messages;
use VarMap\ArrayVarMap;
use Params\Exception\AnnotationClassDoesNotExistException;
use Params\Exception\IncorrectNumberOfParamsException;
use Params\Exception\NoConstructorException;
use Params\Exception\MissingConstructorParameterNameException;
use Params\Exception\PropertyHasMultipleParamAnnotationsException;
use function \Params\createTypeFromAnnotations;

/**
 * @coversNothing
 */
class ParamAnnotationsTest extends BaseTestCase
{

    /**
     * @group wip
     */
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

    /**
     * @group wip
     */
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
