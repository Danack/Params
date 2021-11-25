<?php

declare(strict_types=1);

namespace ParamsTest;

use Params\DataStorage\DataStorage;
use Params\DataStorage\TestArrayDataStorage;
use Params\Exception\ValidationException;
use Params\ExtractRule\GetInt;
use Params\ExtractRule\GetIntOrDefault;
use ParamsTest\BaseTestCase;
use Params\Messages;
use VarMap\ArrayVarMap;
use Params\ProcessRule\AlwaysEndsRule;
use Params\ProcessRule\MaxIntValue;
use Params\ProcessRule\AlwaysErrorsRule;
use Params\ProcessRule\ProcessRule;
use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ProcessedValues;
use Params\InputParameter;
use Params\Exception\UnknownParamException;
use function Params\create;
use function Params\createOrError;
use function Params\createTypeFromAnnotations;
use function Params\processInputParameters;

/**
 * This is a general test suite for integration type stuff.
 *
 * aka, if you're not sure where a test should go, put it here.
 *
 * @coversNothing
 */
class ParamsTest extends BaseTestCase
{
    /**
     *  todo - covers what?
     */
    public function testWorksBasic()
    {
        $rules = [
            new InputParameter(
                'foo',
                new GetIntOrDefault(5)
            )
        ];

        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);

        $problems = processInputParameters(
            $rules,
            $processedValues,
            $dataStorage
        );

        $this->assertNoValidationProblems($problems);
//        $processedValues = \Params\ParamsExecutor::executeRules($rules, new ArrayVarMap([]));
        $this->assertSame(['foo' => 5], $processedValues->getAllValues());
    }

    /**
     *  todo - covers what?
     */
    public function testInvalidInputThrows()
    {
        $arrayVarMap = new ArrayVarMap([]);
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);

        $rules = [
            new InputParameter(
                'foo',
                new GetInt()
            )
        ];

        $this->expectException(\Params\Exception\ValidationException::class);
        // TODO - we should output the keys as well.
        $this->expectExceptionMessage("Value not set.");
        create('Foo', $rules, $dataStorage);
    }

    /**
     *  todo - covers what?
     */
    public function testFinalResultStopsProcessing()
    {
        $finalValue = 123;
        $data = ['foo' => 5];

        $dataStorage = TestArrayDataStorage::fromArray($data);

        $rules = [
            new InputParameter(
                'foo',
                new GetInt(),
                // This rule will stop processing
                new AlwaysEndsRule($finalValue),
                // this rule would give an error if processing was not stopped.
                new MaxIntValue($finalValue - 5)
            )
        ];

        $processedValues = new ProcessedValues();

        $validationProblems = processInputParameters($rules, $processedValues, $dataStorage);
        $this->assertNoValidationProblems($validationProblems);

        $this->assertHasValue($finalValue, 'foo', $processedValues);
    }

    public function testErrorResultStopsProcessing()
    {
        $shouldntBeInvoked = new class($this) implements ProcessRule {
            private $test;
            public function __construct(BaseTestCase $test)
            {
                $this->test = $test;
            }

            public function process($value, ProcessedValues $processedValues, DataStorage $inputStorage) : ValidationResult
            {
                $this->test->fail("This shouldn't be reached.");
                $key = "foo";
                //this code won't be executed.
                return ValidationResult::errorResult($inputStorage, "Shouldn't be called");
            }

            public function updateParamDescription(ParamDescription $paramDescription): void
            {
                // does nothing
            }
        };

        $errorMessage = 'deliberately stopped';
        $data = ['foo' => 100];
        $dataStorage = TestArrayDataStorage::fromArray($data);

        $inputParameters = [
            new InputParameter(
                'foo',
                new GetInt(),
                // This rule will stop processing
                new AlwaysErrorsRule($errorMessage),
                // this rule would give an error if processing was not stopped.
                $shouldntBeInvoked
            )
        ];

        try {
            create('Foo', $inputParameters, $dataStorage);

            $this->fail("This shouldn't be reached, as an exception should have been thrown.");
        }
        catch (ValidationException $validationException) {
            $this->assertValidationProblem(
                '/foo',
                $errorMessage,
                $validationException->getValidationProblems()
            );
        }
    }


    /**
     * @covers ::Params\create
     */
    public function testException()
    {
        $rules = \ParamsTest\Integration\FooParams::getInputParameterList();
        $this->expectException(\Params\Exception\ParamsException::class);

        $dataStorage =  TestArrayDataStorage::fromArraySetFirstValue([]);


        create(\ParamsTest\Integration\FooParams::class, $rules, $dataStorage);
    }

    /**
     * @covers ::Params\create
     */
    public function testWorks()
    {
        $data = ['limit' => 5];
        $dataStorage =  TestArrayDataStorage::fromArray($data);

        $rules = \ParamsTest\Integration\FooParams::getInputParameterList();
        $fooParams = create(
            \ParamsTest\Integration\FooParams::class,
            $rules,
            $dataStorage
        );

        /** @var \ParamsTest\Integration\FooParams $fooParams */
        $this->assertEquals(5, $fooParams->getLimit());
    }

    /**
     * @covers ::Params\createOrError
     */
    public function testCreateOrError_ErrorIsReturned()
    {
        $dataStorage = TestArrayDataStorage::fromArray([]);

        $rules = \ParamsTest\Integration\FooParams::getInputParameterList();
        [$params, $validationProblems] = createOrError(
            \ParamsTest\Integration\FooParams::class,
            $rules,
            $dataStorage
        );
        $this->assertNull($params);

        $this->assertCount(1, $validationProblems);
        /** @var \Params\ValidationProblem $firstProblem */

        $this->assertCount(1, $validationProblems);

        $this->assertValidationProblem(
            '/limit',
            'Value not set.',
            $validationProblems
        );
    }

    /**
     * @covers ::Params\createOrError
     */
    public function testcreateOrError_Works()
    {
        $dataStorage = TestArrayDataStorage::fromArray(['limit' => 5]);

        $rules = \ParamsTest\Integration\FooParams::getInputParameterList();
        [$fooParams, $errors] = createOrError(
            \ParamsTest\Integration\FooParams::class,
            $rules,
            $dataStorage
        );

        $this->assertNoValidationProblems($errors);
        /** @var $fooParams \ParamsTest\Integration\FooParams */
        $this->assertEquals(5, $fooParams->getLimit());
    }

    public function testUnknownInputThrows()
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

        $data = [
            'background_color' => 'red',
            'unknown_color' => 'blue'
        ];

        [$object, $validationProblems] =  \TwoColors::createOrErrorFromArray($data);

        $this->assertNull($object);
        $this->assertValidationProblemRegexp(
            '/',
            Messages::UNKNOWN_INPUT_PARAMETER,
            $validationProblems
        );
    }
}
