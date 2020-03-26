<?php

declare(strict_types=1);

namespace ParamsTest\Exception\Validator;

use Params\ExtractRule\GetInt;
use Params\Param;
use Params\ProcessRule\MaxIntValue;
use ParamsTest\BaseTestCase;
use VarMap\ArrayVarMap;
use Params\ParamsValuesImpl;
use Params\ProcessRule\AlwaysEndsRule;
use Params\Path;

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

//    public function testInvalidInputThrows()
//    {
//        $arrayVarMap = new ArrayVarMap([]);
//        $validator = new ParamsValuesImpl();
//
//        $value = $validator->validateRulesForParam(
//            'foo',
//            $arrayVarMap,
//            new GetInt()
//        );
//
//        $this->assertNull($value);
//        $errors = $validator->getValidationProblems();
//
//        $this->assertEquals(1, count($errors));
//        $this->assertStringMatchesFormat(GetInt::ERROR_MESSAGE, $errors['/foo']);
//    }

    /**
     * @covers \Params\ParamsValuesImpl
     */
    public function testFinalResultStopsProcessing()
    {
        $finalValue = 123;


        $arrayVarMap = new ArrayVarMap(['foo' => 5]);

        $param = new Param(
            'foo',
            new GetInt(),
            // This rule will stop processing
            new AlwaysEndsRule($finalValue),
            // this rule would give an error if processing was not stopped.
            new MaxIntValue($finalValue - 5)
        );

        $validator = new ParamsValuesImpl();

        $errors = $validator->validateParam(
            $param,
            $arrayVarMap,
            Path::initial()
        );

        $this->assertTrue($validator->hasParam('foo'));
        $value = $validator->getParam('foo');

        $this->assertEquals($finalValue, $value);
        $this->assertEmpty($errors);
    }
}
