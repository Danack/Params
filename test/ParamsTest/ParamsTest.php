<?php

declare(strict_types=1);

namespace ParamsTest\Exception\Validator;

use Params\Exception\ValidationException;
use Params\ExtractRule\GetInt;
use Params\ExtractRule\GetIntOrDefault;
use Params\ExtractRule\GetStringOrDefault;
use Params\ProcessRule\MaxLength;
use Params\ProcessRule\SkipIfNull;
use ParamsTest\BaseTestCase;
use VarMap\ArrayVarMap;
use Params\ParamsExecutor;
use Params\ProcessRule\AlwaysEndsRule;
use Params\ProcessRule\MaxIntValue;
use Params\ProcessRule\AlwaysErrorsRule;
use Params\ProcessRule\ProcessRule;
use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ParamsValuesImpl;
use Params\ParamValues;
use Params\Param;
use Params\Path;

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
            new Param(
                'foo',
                new GetIntOrDefault(5)
            )
        ];

        $validator = new ParamsValuesImpl();
        $path = Path::initial();

        $validator->executeRulesWithValidator($rules, new ArrayVarMap([]), $path);

//        $validator = \Params\ParamsExecutor::executeRules($rules, new ArrayVarMap([]));
        $this->assertSame(['foo' => 5], $validator->getParamsValues());
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

        $rules = [
            new Param(
                'foo',
                new GetInt()
            )
        ];

        $this->expectException(\Params\Exception\ValidationException::class);
        // TODO - we should output the keys as well.
        $this->expectExceptionMessage("Value not set.");
        ParamsExecutor::create('Foo', $rules, $arrayVarMap);
    }

    /**
     *  todo - covers what?
     */
    public function testFinalResultStopsProcessing()
    {
        $finalValue = 123;

        $arrayVarMap = new ArrayVarMap(['foo' => 5]);
        $rules = [
            new Param(
                'foo',
                new GetInt(),
                // This rule will stop processing
                new AlwaysEndsRule($finalValue),
                // this rule would give an error if processing was not stopped.
                new MaxIntValue($finalValue - 5)
            )
        ];

        $validator = new ParamsValuesImpl();
        $path = Path::initial();

        $validator->executeRulesWithValidator($rules, $arrayVarMap, $path);

//        $validator = ParamsExecutor::executeRules($rules, $arrayVarMap);
        $this->assertEquals($finalValue, ($validator->getParamsValues())['foo']);
    }

    public function testErrorResultStopsProcessing()
    {
        $shouldntBeInvoked = new class($this) implements ProcessRule {
            private $test;
            public function __construct(BaseTestCase $test)
            {
                $this->test = $test;
            }

            public function process(Path $path, $value, ParamValues $validator) : ValidationResult
            {
                $this->test->fail("This shouldn't be reached.");
                $key = "foo";
                //this code won't be executed.
                return ValidationResult::errorResult(Path::fromName($key), "Shouldn't be called");
            }

            public function updateParamDescription(ParamDescription $paramDescription): void
            {
                // does nothing
            }
        };

        $errorMessage = 'deliberately stopped';

        $arrayVarMap = new ArrayVarMap(['foo' => 100]);
        $rules = [
            new Param(
                'foo',
                new GetInt(),
                // This rule will stop processing
                new AlwaysErrorsRule($errorMessage),
                // this rule would give an error if processing was not stopped.
                $shouldntBeInvoked
            )
        ];

        try {
            ParamsExecutor::create('Foo', $rules, $arrayVarMap);

            $this->fail("This shouldn't be reached, as an exception should have been thrown.");
        }
        catch (ValidationException $validationException) {
            $this->assertValidationProblem(
                'foo',
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
     * @covers \Params\ParamsExecutor::create
     */
    public function testException()
    {
        $arrayVarMap = new ArrayVarMap([]);
        $rules = \ParamsTest\Integration\FooParams::getInputToParamInfoList();
        $this->expectException(\Params\Exception\ParamsException::class);
        \Params\ParamsExecutor::create(\ParamsTest\Integration\FooParams::class, $rules, $arrayVarMap);
    }

    /**
     * @covers \Params\ParamsExecutor::create
     */
    public function testWorks()
    {
        $arrayVarMap = new ArrayVarMap(['limit' => 5]);
        $rules = \ParamsTest\Integration\FooParams::getInputToParamInfoList();
        $fooParams = \Params\ParamsExecutor::create(
            \ParamsTest\Integration\FooParams::class,
            $rules,
            $arrayVarMap
        );
        $this->assertEquals(5, $fooParams->getLimit());
    }

    /**
     * @covers \Params\ParamsExecutor::createOrError
     */
    public function testCreateOrError_ErrorIsReturned()
    {
        $arrayVarMap = new ArrayVarMap([]);
        $rules = \ParamsTest\Integration\FooParams::getInputToParamInfoList();
        [$params, $validationProblems] = \Params\ParamsExecutor::createOrError(
            \ParamsTest\Integration\FooParams::class,
            $rules,
            $arrayVarMap
        );
        $this->assertNull($params);

        $this->assertCount(1, $validationProblems);
        /** @var \Params\ValidationProblem $firstProblem */

        $this->assertCount(1, $validationProblems);

        $this->assertValidationProblem(
            'limit',
            'Value not set.',
            $validationProblems
        );
    }

    /**
     * @covers \Params\ParamsExecutor::createOrError
     */
    public function testcreateOrError_Works()
    {
        $arrayVarMap = new ArrayVarMap(['limit' => 5]);
        $rules = \ParamsTest\Integration\FooParams::getInputToParamInfoList();
        [$fooParams, $errors] = \Params\ParamsExecutor::createOrError(
            \ParamsTest\Integration\FooParams::class,
            $rules,
            $arrayVarMap
        );
        $this->assertCount(0, $errors);
        /** @var $fooParams \ParamsTest\Integration\FooParams */
        $this->assertEquals(5, $fooParams->getLimit());
    }
}
