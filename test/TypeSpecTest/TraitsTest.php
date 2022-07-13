<?php

declare(strict_types=1);

namespace TypeSpecTest;

use VarMap\ArrayVarMap;
use TypeSpecTest\Integration\FooParamsCreateFromVarMap;
use TypeSpecTest\Integration\FooParamsCreateOrErrorFromVarMap;

/**
 * @coversNothing
 */
class TraitsTest extends BaseTestCase
{
    /**
     * @covers \TypeSpec\Create\CreateFromVarMap
     */
    public function testCreateFromVarMap()
    {
        $limitValue = 13;
        $varMap = new ArrayVarMap(['limit' => $limitValue]);
        $fooParams = FooParamsCreateFromVarMap::createFromVarMap($varMap);
        $this->assertInstanceOf(FooParamsCreateFromVarMap::class, $fooParams);
        $this->assertEquals($limitValue, $fooParams->getLimit());
    }

    /**
     * @covers \TypeSpec\Create\CreateOrErrorFromVarMap
     */
    public function testCreateOrErrorFromVarMap()
    {
        $limitValue = 13;
        $varMap = new ArrayVarMap(['limit' => $limitValue]);
        [$fooParams, $errors] = FooParamsCreateOrErrorFromVarMap::createOrErrorFromVarMap($varMap);
        $this->assertEmpty($errors);
        $this->assertInstanceOf(FooParamsCreateOrErrorFromVarMap::class, $fooParams);
        /** @var $fooParams FooParamsCreateOrErrorFromVarMap */
        $this->assertEquals($limitValue, $fooParams->getLimit());
    }
}
