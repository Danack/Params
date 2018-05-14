<?php

declare(strict_types=1);

namespace ParamsTest\Exception\Validator;

use Params\Exception\ValidationException;
use Params\Rule\CheckSet;
use Params\Rule\CheckSetOrDefault;
use Params\Rule\SkipIfNull;
use ParamsTest\BaseTestCase;
use VarMap\ArrayVarMap;
use Params\Params;
use Params\Rule\AlwaysEndsRule;
use Params\Rule\MaxIntValue;
use Params\Rule\AlwaysErrorsRule;
use Params\Rule;
use Params\ValidationResult;

class ParamsTest extends BaseTestCase
{
    public function testMissingRuleThrows()
    {
        $rules = [
            'foo' => []
        ];

        $this->expectException(\Params\Exception\ParamsException::class);
        \Params\Params::validate($rules);
    }

    public function testInvalidInputThrows()
    {
        $arrayVarMap = new ArrayVarMap([]);

        $rules = [
            'foo' => [
                new CheckSet($arrayVarMap)
            ]
        ];

        $this->expectException(\Params\Exception\ValidationException::class);
        $this->expectExceptionMessage("Value not set for foo");
        Params::validate($rules);
    }


    public function testFinalResultStopsProcessing()
    {
        $finalValue = 123;

        $arrayVarMap = new ArrayVarMap(['foo' => 5]);
        $rules = [
            'foo' => [
                new CheckSet($arrayVarMap),
                // This rule will stop processing
                new AlwaysEndsRule($finalValue),
                // this rule would give an error if processing was not stopped.
                new MaxIntValue($finalValue - 5)
            ]
        ];

        $values = Params::validate($rules);
        $this->assertEquals($finalValue, $values[0]);
    }

    public function testErrorResultStopsProcessing()
    {
        $shouldntBeInvoked = new class($this)  implements Rule {
            private $test;
            public function __construct(BaseTestCase $test)
            {
                $this->test = $test;
            }

            public function __invoke(string $name, $value): ValidationResult
            {
                $this->test->fail("This shouldn't be reached.");
                //this code won't be executed.
                return ValidationResult::errorResult("Shouldn't be called");
            }
        };

        $errorMessage = 'deliberately stopped';

        $arrayVarMap = new ArrayVarMap(['foo' => 100]);
        $rules = [
            'foo' => [
                new CheckSet($arrayVarMap),
                // This rule will stop processing
                new AlwaysErrorsRule($errorMessage),
                // this rule would give an error if processing was not stopped.
                $shouldntBeInvoked
            ]
        ];

        try {
            $values = Params::validate($rules);
            $this->fail("This shouldn't be reached, as an exception should have been thrown.");
        }
        catch (ValidationException $validationException) {
            $validationProblems = $validationException->getValidationProblems();
            $this->assertEquals(1, count($validationProblems));
            $this->assertEquals($errorMessage, $validationProblems[0]);
        }
    }

    public function testSkipOrNullCoverage()
    {
        $arrayVarMap = new ArrayVarMap([]);
        $rules = [
            'foo' => [
                new CheckSetOrDefault(null, $arrayVarMap),
                new SkipIfNull()
            ]
        ];

        list($foo) = Params::validate($rules);
        $this->assertNull($foo);
    }

}
