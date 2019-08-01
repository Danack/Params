<?php

declare(strict_types=1);

namespace ParamsTest\Exception\Validator;

use Params\FirstRule\GetInt;
use Params\SubsequentRule\MaxIntValue;
use ParamsTest\BaseTestCase;
use VarMap\ArrayVarMap;
use Params\ParamsValidator;
use Params\SubsequentRule\AlwaysEndsRule;

/**
 * @coversNothing
 */
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
        $errors = $validator->getValidationProblems();

        $this->assertEquals(1, count($errors));
        $this->assertStringMatchesFormat(GetInt::ERROR_MESSAGE, $errors['/foo']);
    }

    /**
     * @covers \Params\ParamsValidator
     */
    public function testFinalResultStopsProcessing()
    {
        $finalValue = 123;

        $arrayVarMap = new ArrayVarMap(['foo' => 5]);
        $subsequentRules = [
            // This rule will stop processing
            new AlwaysEndsRule($finalValue),
            // this rule would give an error if processing was not stopped.
            new MaxIntValue($finalValue - 5)
        ];
        $validator = new ParamsValidator();

        [$value, $errors] = $validator->validateRulesForParam(
            'foo',
            $arrayVarMap,
            new GetInt(),
            ...$subsequentRules
        );

        $this->assertEquals($finalValue, $value);
        $this->assertEmpty($errors);
    }
}
