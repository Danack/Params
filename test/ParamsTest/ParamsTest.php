<?php

declare(strict_types=1);

namespace ParamsTest\Exception\Validator;

use Params\Exception\ValidationException;
use Params\FirstRule\GetInt;
use Params\FirstRule\GetIntOrDefault;
use Params\FirstRule\GetStringOrDefault;
use Params\SubsequentRule\MaxLength;
use Params\SubsequentRule\SkipIfNull;
use ParamsTest\BaseTestCase;
use VarMap\ArrayVarMap;
use Params\Params;
use Params\SubsequentRule\AlwaysEndsRule;
use Params\SubsequentRule\MaxIntValue;
use Params\SubsequentRule\AlwaysErrorsRule;
use Params\SubsequentRule\SubsequentRule;
use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ValidationErrors;
use Params\ParamsValidator;
use Params\Exception\ParamsException;
use Params\Exception\RulesEmptyException;
use Params\ParamValues;

/**
 * @coversNothing
 */
class ParamsTest extends BaseTestCase
{
    /**
     * @covers \Params\Params::executeRules
     */
    public function testWorksBasic()
    {
        $rules = [
            'foo' => [
                new GetIntOrDefault(5)
            ]
        ];

        $validator = \Params\Params::executeRules($rules, new ArrayVarMap([]));
        $this->assertSame(['foo' => 5], $validator->getParamsValues());
    }

    /**
     * @covers \Params\Params::executeRules
     * @covers \Params\Params::executeRulesWithValidator
     */
    public function testMissingRuleThrows()
    {
        $rules = [
            'foo' => []
        ];

        $this->expectException(RulesEmptyException::class);
        \Params\Params::executeRules($rules, new ArrayVarMap([]));
    }

    /**
     * @covers \Params\Params::executeRules
     */
    public function testBadFirstRuleThrows()
    {
        $rules = [
            'foo' => [
                new MaxLength(10)
            ]
        ];

        $this->expectException(\Params\Exception\ParamsException::class);
        $this->expectExceptionMessage(ParamsException::ERROR_FIRST_RULE_MUST_IMPLEMENT_FIRST_RULE);
        \Params\Params::executeRules($rules, new ArrayVarMap([]));
    }

    /**
     * @covers \Params\Params::executeRules
     */
    public function testInvalidInputThrows()
    {
        $arrayVarMap = new ArrayVarMap([]);

        $rules = [
            'foo' => [
                new GetInt()
            ]
        ];

        $this->expectException(\Params\Exception\ValidationException::class);
        $this->expectExceptionMessage("Value not set for foo");
        Params::create('Foo', $rules, $arrayVarMap);
    }

    /**
     * @covers \Params\Params::executeRules
     */
    public function testFinalResultStopsProcessing()
    {
        $finalValue = 123;

        $arrayVarMap = new ArrayVarMap(['foo' => 5]);
        $rules = [
            'foo' => [
                new GetInt(),
                // This rule will stop processing
                new AlwaysEndsRule($finalValue),
                // this rule would give an error if processing was not stopped.
                new MaxIntValue($finalValue - 5)
            ]
        ];

        $validator = Params::executeRules($rules, $arrayVarMap);
        $this->assertEquals($finalValue, ($validator->getParamsValues())['foo']);
    }

    /**
     * @covers \Params\Params::executeRules
     */
    public function testErrorResultStopsProcessing()
    {
        $shouldntBeInvoked = new class($this) implements SubsequentRule {
            private $test;
            public function __construct(BaseTestCase $test)
            {
                $this->test = $test;
            }

            public function process(string $name, $value, ParamValues $validator) : ValidationResult
            {
                $this->test->fail("This shouldn't be reached.");
                //this code won't be executed.
                return ValidationResult::errorResult("Shouldn't be called");
            }

            public function updateParamDescription(ParamDescription $paramDescription)
            {
                // does nothing
            }
        };

        $errorMessage = 'deliberately stopped';

        $arrayVarMap = new ArrayVarMap(['foo' => 100]);
        $rules = [
            'foo' => [
                new GetInt(),
                // This rule will stop processing
                new AlwaysErrorsRule($errorMessage),
                // this rule would give an error if processing was not stopped.
                $shouldntBeInvoked
            ]
        ];

        try {
            Params::create('Foo', $rules, $arrayVarMap);

            $this->fail("This shouldn't be reached, as an exception should have been thrown.");
        }
        catch (ValidationException $validationException) {
            $validationProblems = $validationException->getValidationProblems();
            $this->assertEquals(1, count($validationProblems));
            $this->assertEquals($errorMessage, $validationProblems[0]);
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
     * @covers \Params\Params::create
     */
    public function testException()
    {
        $arrayVarMap = new ArrayVarMap([]);
        $rules = \ParamsTest\Integration\FooParams::getRules();
        $this->expectException(\Params\Exception\ParamsException::class);
        \Params\Params::create(\ParamsTest\Integration\FooParams::class, $rules, $arrayVarMap);
    }

    /**
     * @covers \Params\Params::create
     */
    public function testWorks()
    {
        $arrayVarMap = new ArrayVarMap(['limit' => 5]);
        $rules = \ParamsTest\Integration\FooParams::getRules();
        $fooParams = \Params\Params::create(
            \ParamsTest\Integration\FooParams::class,
            $rules,
            $arrayVarMap
        );
        $this->assertEquals(5, $fooParams->getLimit());
    }

    /**
     * @covers \Params\Params::createOrError
     */
    public function testCreateOrError_ErrorIsReturned()
    {
        $arrayVarMap = new ArrayVarMap([]);
        $rules = \ParamsTest\Integration\FooParams::getRules();
        [$params, $validationErrors] = \Params\Params::createOrError(
            \ParamsTest\Integration\FooParams::class,
            $rules,
            $arrayVarMap
        );
        $this->assertNull($params);
        /** @var ValidationErrors $validationErrors */
//        $this->assertInstanceOf(ValidationErrors::class, $validationErrors);
//        $errors = $validationErrors->getValidationProblems();
        $this->assertCount(1, $validationErrors);
        $this->assertStringMatchesFormat('Value not set for %s.', $validationErrors[0]);
    }

    /**
     * @covers \Params\Params::createOrError
     */
    public function testcreateOrError_Works()
    {
        $arrayVarMap = new ArrayVarMap(['limit' => 5]);
        $rules = \ParamsTest\Integration\FooParams::getRules();
        [$fooParams, $errors] = \Params\Params::createOrError(
            \ParamsTest\Integration\FooParams::class,
            $rules,
            $arrayVarMap
        );
        $this->assertCount(0, $errors);
        /** @var $fooParams \ParamsTest\Integration\FooParams */
        $this->assertEquals(5, $fooParams->getLimit());
    }
}
