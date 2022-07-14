<?php

declare(strict_types=1);

namespace TypeSpecTest;

use TypeSpec\ExtractRule\GetStringOrDefault;
use TypeSpec\InputTypeSpec;
use TypeSpec\Messages;
use TypeSpec\ProcessRule\ImagickIsRgbColor;
use TypeSpecTest\Integration\IntArrayParams;
use VarMap\ArrayVarMap;
use TypeSpec\Exception\AnnotationClassDoesNotExistException;
use TypeSpec\Exception\IncorrectNumberOfParametersException;
use TypeSpec\Exception\NoConstructorException;
use TypeSpec\Exception\MissingConstructorParameterNameException;
use TypeSpec\Exception\PropertyHasMultipleInputTypeSpecAnnotationsException;
use function TypeSpec\createOrError;

/**
 * @coversNothing
 * TODO - this class duplicates some tests that should be elsewhere.
 */
class ParamAnnotationsTest extends BaseTestCase
{
    /**
     * @group deadish
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

        $inputParameters = $threeColors::getInputTypeSpecList();

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

            $this->assertInstanceOf(InputTypeSpec::class, $inputParameter);

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


    /**
     * @group deadish
     * @group needs_fixing
     */
    public function testMissingConstructorParamErrors()
    {
        $varMap = new ArrayVarMap([
            'background_color' => 'red',
            'stroke_color' => 'rgb(255, 0, 255)',
            'fill_color' => 'white',
        ]);

        // \Params\Exception\IncorrectNumberOfParamsException::wrongNumber
        // TODO - set expected exception
        $this->expectException(IncorrectNumberOfParametersException::class);
        createTypeFromAnnotations($varMap, \ThreeColorsMissingConstructorParam::class);
    }

    /**
     * @group deadish
     */
    public function testMissingPropertyParamErrors()
    {
        $varMap = new ArrayVarMap([
            'background_color' => 'red',
            'stroke_color' => 'rgb(255, 0, 255)',
//            'fill_color' => 'white',
        ]);

        $this->expectException(IncorrectNumberOfParametersException::class);
        $this->markTestSkipped("This test is needed.");
        createTypeFromAnnotations($varMap, \ThreeColorsMissingPropertyParam::class);
    }

    /**
     * @group deadish
     */
    public function testMissingConstructor()
    {
        $varMap = new ArrayVarMap([
            'background_color' => 'red',
        ]);

        $this->expectException(NoConstructorException::class);
        $this->expectExceptionMessageMatchesTemplateString(
            Messages::CLASS_LACKS_CONSTRUCTOR
        );

        createTypeFromAnnotations($varMap, \OneColorNoConstructor::class);
    }


    /**
     * @group deadish
     */
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

    /**
     * @group deadish
     */
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

    /**
     * @group deadish
     */
    public function testOneParamWithOneOtherPropertyName()
    {
        $varMap = new ArrayVarMap([
            'background_color' => 'red',
        ]);

        createTypeFromAnnotations($varMap, \TwoColors::class);
    }

    /**
     * @group deadish
     */
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

    /**
     * @group deadish
     */
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


    /**
     * @group deadish
     */
    public function testMultipleParamsErrors()
    {
        $varMap = new ArrayVarMap([
            'background_color' => 'red',
        ]);

        $this->expectException(PropertyHasMultipleInputTypeSpecAnnotationsException::class);
        $this->expectExceptionMessageMatches('#.*background_color.*#iu');

        createTypeFromAnnotations($varMap, \MultipleParamAnnotations::class);
    }


    /**
     * @group wip
     */
    public function testCorrectSpellingIsUsed()
    {
        $this->markTestSkipped("Preferred behaviour is not known for this feature.");
        // Okay, so theoretically, detecting that unknown parameters are present
        // in the data being parsed/validated is a useful thing to do.
        //
        // However it's also really annoying when the source data is not under
        // direct programmer control, and is coming from something like $_GET
        // parameters. It's common to add a random parameter to cache-bust, and
        // then getting an error message of 'time=1231231231231 is unknown param'
        // is so annoying.

        // This is correct - it uses the name as per the annotation
        $result = \OneColorGetsCorrectSpelling::createFromArray([
            'backgroundColor' => 'red'
        ]);
        $this->assertInstanceOf(\OneColorGetsCorrectSpelling::class, $result);

        // This is incorrect - it uses the name as per the property name
        [$object, $validationProblems] = \OneColorGetsCorrectSpelling::createOrErrorFromArray([
            'this_is_invalid' => 'red'
        ]);
        $this->assertInstanceOf(\OneColorGetsCorrectSpelling::class, $object);
        $this->assertSame(
            \OneColorGetsCorrectSpelling::DEFAULT_COLOR,
            $object->getBackgroundColor()
        );

        $this->assertCount(
            1,
            $validationProblems,
            "if zero, unknown param 'this_is_invalid' not being shown as unknown."
        );
        $this->assertValidationProblemRegexp(
            '/',
            Messages::UNKNOWN_INPUT_PARAMETER,
            $validationProblems
        );
    }
}
