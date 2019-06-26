<?php

declare(strict_types=1);

namespace ParamsTest\Exception\Validator;

use Params\FirstRule\GetInt;
use Params\SubsequentRule\MaxIntValue;
use ParamsTest\BaseTestCase;
use VarMap\ArrayVarMap;
use Params\ParamsValidator;
use Params\SubsequentRule\AlwaysEndsRule;

class ParamsValidatorTest extends BaseTestCase
{

    public function testMissingRuleThrows()
    {
        $this->markTestSkipped("does this provide useful info?");
//        $validator = new ParamsValidator();
//        $this->expectException(\Params\Exception\ParamsException::class);
//        $validator->validate('foobar', []);
    }

    public function testInvalidInputThrows()
    {
        $arrayVarMap = new ArrayVarMap([]);


        $validator = new ParamsValidator();

        $value = $validator->validateRulesForParam(
            'foo',
            $arrayVarMap,
            new GetInt()
        );

        $this->assertNull($value);
        $validationProblems = $validator->getValidationProblems();
        $this->assertNotNull($validationProblems);

        $errors = $validationProblems->getValidationProblems();
        $this->assertEquals(1, count($errors));
        $this->assertStringMatchesFormat(GetInt::ERROR_MESSAGE, $errors[0]);
    }


    public function testFinalResultStopsProcessing()
    {
        $this->markTestSkipped("does this provide useful info?");
//        $finalValue = 123;
//
//        $arrayVarMap = new ArrayVarMap(['foo' => 5]);
//        $rules = [
//            new GetInt(),
//            // This rule will stop processing
//            new AlwaysEndsRule($finalValue),
//            // this rule would give an error if processing was not stopped.
//            new MaxIntValue($finalValue - 5)
//        ];
//
//        $validator = new ParamsValidator();
//
//        $value = $validator->validate('foo', $rules);
//
//        $this->assertEquals($finalValue, $value);
//        $this->assertEmpty($validator->getValidationProblems());
    }
}
