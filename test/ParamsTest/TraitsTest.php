<?php

declare(strict_types=1);

namespace ParamsTest;

use ParamsTest\BaseTestCase;
use VarMap\ArrayVarMap;
use ParamsTest\FooParamsCreateFromVarMap;
use Params\Input;
use Params\ValueInput;
use ParamsTest\BarParamsCreateFromInput;

class TraitsTest extends BaseTestCase
{
    public function testCreateFromVarMap()
    {
        $limitValue = 13;
        $varMap = new ArrayVarMap(['limit' => $limitValue]);
        $fooParams = FooParamsCreateFromVarMap::createFromVarMap($varMap);
        $this->assertInstanceOf(FooParamsCreateFromVarMap::class, $fooParams);
        $this->assertEquals($limitValue, $fooParams->getLimit());
    }

    public function testCreateOrErrorFromVarMap()
    {
        $limitValue = 13;
        $varMap = new ArrayVarMap(['limit' => $limitValue]);
        [$fooParams, $errors] = FooParamsCreateOrErrorFromVarMap::createOrErrorFromVarMap($varMap);
        $this->assertNull($errors);
        $this->assertInstanceOf(FooParamsCreateOrErrorFromVarMap::class, $fooParams);
        /** @var $fooParams FooParamsCreateOrErrorFromVarMap */
        $this->assertEquals($limitValue, $fooParams->getLimit());
    }

    public function testCreateFromInput()
    {
        $inputValues = [1, 2, 3];
        $varMap = new ValueInput($inputValues);
        $fooParams = BarParamsCreateFromInput::createFromInput($varMap);
        $this->assertInstanceOf(BarParamsCreateFromInput::class, $fooParams);
        $this->assertEquals($inputValues, $fooParams->getValues());
    }

    public function testCreateOrErrorFromInput()
    {
        $inputValues = [1, 2, 3];
        $varMap = new ValueInput($inputValues);
        [$fooParams, $errors] = BarParamsCreateOrErrorFromInput::createFromInput($varMap);
        $this->assertNull($errors);
        $this->assertInstanceOf(BarParamsCreateOrErrorFromInput::class, $fooParams);
        $this->assertEquals($inputValues, $fooParams->getValues());
    }
}
