<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use ParamsTest\BaseTestCase;
use Params\FirstRule\GetArrayOfType;
use ParamsTest\Integration\ItemParams;
use VarMap\ArrayVarMap;
use Params\ParamsValidator;

/**
 * @coversNothing
 * @group wip
 */
class GetArrayOfTypeTest extends BaseTestCase
{

    /**
     * @covers \Params\FirstRule\GetArrayOfType
     */
    public function testWorks()
    {
        $data = [
            'items' => [
                ['foo' => 5, 'bar' => 'Hello world']
            ],
        ];

        $rule = new GetArrayOfType(ItemParams::class);
        $validator = new ParamsValidator();
        $result = $rule->process('items', new ArrayVarMap($data), $validator);

        $this->assertFalse($result->isFinalResult());
//        $this->assertEquals("Value not set for 'items'.", $result->getProblemMessage());

        $this->assertCount(1, $result->getValue());
        $item = ($result->getValue())[0];
        $this->assertInstanceOf(ItemParams::class, $item);
        /** @var ItemParams $item */
        $this->assertSame(5, $item->getFoo());
        $this->assertSame('Hello world', $item->getBar());

        $this->assertNull($result->getProblemMessage());
    }



    /**
     * @covers \Params\FirstRule\GetArrayOfType
     */
    public function testMissingArrayErrors()
    {
        $data = [];

        $rule = new GetArrayOfType(ItemParams::class);
        $validator = new ParamsValidator();
        $result = $rule->process('items', new ArrayVarMap($data), $validator);
        $this->assertTrue($result->isFinalResult());
        $this->assertEquals("Value not set for 'items'.", $result->getProblemMessage());
        $this->assertNull($result->getValue());
    }


    /**
     * @covers \Params\FirstRule\GetArrayOfType
     */
    public function testScalarInsteadOfArrayErrors()
    {
        $data = [
            'items' => 'a banana'
        ];

        $rule = new GetArrayOfType(ItemParams::class);
        $validator = new ParamsValidator();
        $result = $rule->process('items', new ArrayVarMap($data), $validator);
        $this->assertTrue($result->isFinalResult());
        $this->assertEquals(
            "Value set for 'items' must be an array.",
            $result->getProblemMessage()
        );
        $this->assertNull($result->getValue());
    }



    /**
     * @covers \Params\FirstRule\GetArrayOfType
     */
    public function testSingleError()
    {
        $data = [
            'items' => [
                ['foo' => 5, 'bar' => false]
            ],
        ];

        $rule = new GetArrayOfType(ItemParams::class);
        $validator = new ParamsValidator();
        $result = $rule->process('items', new ArrayVarMap($data), $validator);

        $this->assertTrue($result->isFinalResult());
        $this->assertNull($result->getValue());


        $this->assertSame(
            "Error in items. [0] string for 'bar' too short, min chars is 4",
            $result->getProblemMessage()
        );
    }

    /**
     * @covers \Params\FirstRule\GetArrayOfType
     */
    public function testMultipleErrors()
    {
        $data = [
            'items' => [
                ['foo' => 5, 'bar' => 'foo'],
                ['foo' => 101, 'bar' => 'world']
            ],
        ];

        $validator = new ParamsValidator();
        $rule = new GetArrayOfType(ItemParams::class);
        $result = $rule->process('items', new ArrayVarMap($data), $validator);

        $this->assertTrue($result->isFinalResult());
        $this->assertNull($result->getValue());


        $this->assertSame(
            "Errors in items. [0] string for 'bar' too short, min chars is 4. [1] Value too large. Max allowed is 100",
            $result->getProblemMessage()
        );
    }

}
