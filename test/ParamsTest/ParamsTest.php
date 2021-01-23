<?php

declare(strict_types=1);

namespace ParamsTest;

use Params\InputStorage\InputStorage;
use Params\InputStorage\ArrayInputStorage;
use Params\Exception\ValidationException;
use Params\ExtractRule\GetInt;
use Params\ExtractRule\GetIntOrDefault;
use ParamsTest\BaseTestCase;
use VarMap\ArrayVarMap;
use Params\ProcessRule\AlwaysEndsRule;
use Params\ProcessRule\MaxIntValue;
use Params\ProcessRule\AlwaysErrorsRule;
use Params\ProcessRule\ProcessRule;
use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ProcessedValues;
use Params\InputParameter;
use function Params\create;
use function Params\createOrError;
use function Params\processInputParameters;

/**
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
        $dataLocator = ArrayInputStorage::fromArraySetFirstValue([]);

        $problems = processInputParameters(
            $rules,
            $processedValues,
            $dataLocator
        );

        $this->assertNoValidationProblems($problems);
//        $processedValues = \Params\ParamsExecutor::executeRules($rules, new ArrayVarMap([]));
        $this->assertSame(['foo' => 5], $processedValues->getAllValues());
    }

//    /**
//     * @covers \Params\Params::executeRules
//     * @covers \Params\Params::executeRulesWithValidator
//     */
//    public function testMissingRuleThrows()
//    {
//        $rules = [
//
//            'foo' => []
//        ];
//
//        $this->expectException(RulesEmptyException::class);
//        \Params\Params::executeRules($rules, new ArrayVarMap([]));
//    }

//    /**
//     * @covers \Params\Params::executeRules
//     */
//    public function testBadFirstRuleThrows()
//    {
//        $rules = [
//            'foo' => [
//                new MaxLength(10)
//            ]
//        ];
//
//        $this->expectException(\Params\Exception\ParamsException::class);
//        $this->expectExceptionMessage(ParamsException::ERROR_FIRST_RULE_MUST_IMPLEMENT_FIRST_RULE);
//        \Params\Params::executeRules($rules, new ArrayVarMap([]));
//    }

    /**
     *  todo - covers what?
     */
    public function testInvalidInputThrows()
    {
        $arrayVarMap = new ArrayVarMap([]);
        $dataLocator = ArrayInputStorage::fromArraySetFirstValue([]);

        $rules = [
            new InputParameter(
                'foo',
                new GetInt()
            )
        ];

        $this->expectException(\Params\Exception\ValidationException::class);
        // TODO - we should output the keys as well.
        $this->expectExceptionMessage("Value not set.");
        create('Foo', $rules, $dataLocator);
    }

    /**
     *  todo - covers what?
     */
    public function testFinalResultStopsProcessing()
    {
        $finalValue = 123;
        $data = ['foo' => 5];

        $dataLocator = ArrayInputStorage::fromArray($data);

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

        $validationProblems = processInputParameters($rules, $processedValues, $dataLocator);
        $this->assertNoValidationProblems($validationProblems);

        $this->assertHasValue($finalValue, 'foo', $processedValues);
//        $processedValues = ParamsExecutor::executeRules($rules, $arrayVarMap);
//        $this->assertEquals($finalValue, ($processedValues->getAllValues())['foo']);
    }

    public function testErrorResultStopsProcessing()
    {
        $shouldntBeInvoked = new class($this) implements ProcessRule {
            private $test;
            public function __construct(BaseTestCase $test)
            {
                $this->test = $test;
            }

            public function process($value, ProcessedValues $processedValues, InputStorage $inputStorage) : ValidationResult
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
        $dataLocator = ArrayInputStorage::fromArray($data);

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
            create('Foo', $inputParameters, $dataLocator);

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

//    /**
//     * @covers \Params\Params::validate
//     */
//    public function testSkipOrNullCoverage()
//    {
//        $arrayVarMap = new ArrayVarMap([]);
//        $rules = [
//            'foo' => [
//                new GetStringOrDefault(null),
//                new SkipIfNull()
//            ]
//        ];
//
//        list($foo) = Params::validate($rules);
//        $this->assertNull($foo);
//    }

    /**
     * @covers ::Params\create
     */
    public function testException()
    {
        $rules = \ParamsTest\Integration\FooParams::getInputParameterList();
        $this->expectException(\Params\Exception\ParamsException::class);

        $dataLocator =  ArrayInputStorage::fromArraySetFirstValue([]);


        create(\ParamsTest\Integration\FooParams::class, $rules, $dataLocator);
    }

    /**
     * @covers ::Params\create
     */
    public function testWorks()
    {
        $data = ['limit' => 5];
        $dataLocator =  ArrayInputStorage::fromArray($data);

        $rules = \ParamsTest\Integration\FooParams::getInputParameterList();
        $fooParams = create(
            \ParamsTest\Integration\FooParams::class,
            $rules,
            $dataLocator
        );

        /** @var \ParamsTest\Integration\FooParams $fooParams */
        $this->assertEquals(5, $fooParams->getLimit());
    }

    /**
     * @covers ::Params\createOrError
     */
    public function testCreateOrError_ErrorIsReturned()
    {
        $dataStorage = ArrayInputStorage::fromArray([]);

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
        $dataStorage = ArrayInputStorage::fromArray(['limit' => 5]);

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
}
