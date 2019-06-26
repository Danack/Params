<?php

declare(strict_types=1);

namespace ParamsTest\Exception\Validator;

use Params\Exception\ValidationException;
use Params\FirstRule\GetInt;
use Params\FirstRule\GetStringOrDefault;
use Params\SubsequentRule\SkipIfNull;
use ParamsTest\BaseTestCase;
use VarMap\ArrayVarMap;
use Params\Params;
use Params\SubsequentRule\AlwaysEndsRule;
use Params\SubsequentRule\MaxIntValue;
use Params\SubsequentRule\AlwaysErrorsRule;
use Params\SubsequentRule;
use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ValueInput;

/**
 * @coversNothing
 */
class ValueInputTest extends BaseTestCase
{
    /**
     * @covers \Params\ValueInput
     */
    public function testMissingRuleThrows()
    {
        $value = ['abc', 123];
        $valueInput = new ValueInput($value);
        $this->assertEquals($value, $valueInput->get());
    }
}
